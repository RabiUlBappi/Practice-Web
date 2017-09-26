<?php
  

	function myQuery($query,$bindings,$conn){
    global $conn;
		$stmt=$conn->prepare($query);
		$stmt->execute($bindings);
		//return ($stmt->rowCount()>0)?$stmt:false;
    return $stmt;
	}

  // function insert(){}
  // function select(){} replaced by search()
  // function update(){}
  // function delete(){}

  //<!-- Select Categories -->
  function selectCategory(){
    global $conn;
    $sql = myQuery("SELECT*FROM category",array(),$conn);
    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }
  function selectSubcategory($cat){
    global $conn;
    $sql = myQuery("SELECT*FROM  subcategory WHERE category_id=(SELECT category_id 
                                                                  FROM category
                                                                  WHERE category_name=:category_name)",
                      array(':category_name'=>$cat),
                      $conn);
    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }
  function selectProductType($cat,$subcat){
    global $conn;
    $sql = myQuery("SELECT*FROM  product_type WHERE subcategory_id=(SELECT subcategory_id 
                                                                    FROM subcategory
                                                                    WHERE subcategory_name=:subcategory_name
                                                                    AND category_id=(SELECT category_id
                                                                                     FROM category
                                                                                     WHERE category_name=:category_name
                                                                                    )
                                                                    )",
                    array(':subcategory_name'=>$subcat,':category_name'=>$cat),$conn
                  );
    return $sql->fetchAll(PDO::FETCH_ASSOC);
  }

  //find names
  function find_product_type_name($pt_id){
    global $conn;
    $sql = myQuery("SELECT product_type_name 
                  FROM product_type
                  WHERE product_type_id=:product_type_id LIMIT 1",
                  array(':product_type_id'=>$pt_id),$conn);
    return $sql->fetch();
  }

  function find_subcategory_name($pt_id){
    global $conn;
    $sql = myQuery("SELECT subcategory_name
                  FROM subcategory
                  WHERE subcategory_id=(SELECT subcategory_id
                                        FROM product_type
                                        WHERE product_type_id=:product_type_id
                                        LIMIT 1
                                       ) 
                  LIMIT 1",
           array(':product_type_id'=>$pt_id),$conn);
    return $sql->fetch();
  }

  function find_category_name($pt_id){
    global $conn;
    $sql = myQuery("SELECT category_name
                    FROM category
                    WHERE category_id=(SELECT category_id
                                       FROM subcategory
                                       WHERE subcategory_id=( SELECT subcategory_id
                                                              FROM product_type
                                                              WHERE product_type_id=:product_type_id
                                                              LIMIT 1
                                                             )
                                       LIMIT 1
                                      )
                    LIMIT 1",
           array(':product_type_id'=>$pt_id),$conn);
    return $sql->fetch();
  }


  function search($offset='',$limit='',$qa){
    global $conn;

    $q="SELECT product_id,
               product_name,
               product_description,
               unit_price,
               date_added,
               products.product_type_id,
               product_type_name,
               subcategory.subcategory_id,
               subcategory_name,
               category.category_id,
               category_name
        FROM products 
        INNER JOIN product_type 
        ON products.product_type_id=product_type.product_type_id 
        INNER JOIN subcategory 
        ON product_type.subcategory_id=subcategory.subcategory_id 
        INNER JOIN category 
        ON subcategory.category_id=category.category_id ";
        
    $q_ex = " WHERE ( 
                 product_name        LIKE :query
              OR product_description LIKE :query
              OR product_type_name   LIKE :query
              OR subcategory_name    LIKE :query ";

    if(empty($offset) && empty($limit)) $q_ord_lmt = " ORDER BY date_added DESC ";
    else $q_ord_lmt = " ORDER BY date_added DESC LIMIT ".$offset.",".$limit;

    if(!empty($qa['query']) && !empty($qa['cat_name']) && $qa['cat_name']!='all'){
      $sql = myQuery($q.$q_ex." ) AND category_name=:category_name ".$q_ord_lmt,array(':query'=>'%'.$qa['query'].'%',':category_name'=>$qa['cat_name']),$conn);
    }
    elseif(!empty($qa['cat_name']) && empty($qa['query'])){
      $sql = myQuery($q." WHERE category_name=:category_name ".$q_ord_lmt,
                          array(':category_name'=>$qa['cat_name']),$conn);
    }
    elseif(!empty($qa['query'])){
      $sql = myQuery($q.$q_ex." OR category_name LIKE :query) ".$q_ord_lmt,array(':query'=>'%'.$qa['query'].'%'),$conn);
    }
    elseif(!empty($qa['cat_id'])){
      $sql = myQuery($q." WHERE category.category_id = :category_id ".$q_ord_lmt,array(':category_id'=>$qa['cat_id']),$conn);
    }
    elseif(!empty($qa['scat_id'])){
      $sql = myQuery($q." WHERE subcategory.subcategory_id = :subcategory_id ".$q_ord_lmt,array(':subcategory_id'=>$qa['scat_id']),$conn);
    }
    elseif(!empty($qa['pt_id'])){
      $sql = myQuery($q." WHERE products.product_type_id = :product_type_id ".$q_ord_lmt,array(':product_type_id'=>$qa['pt_id']),$conn);
    }
    else{$sql = myQuery($q.$q_ord_lmt,array(),$conn);}

    return $sql;
  }
?>