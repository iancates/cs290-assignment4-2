<html>
    <form method="POST" id="add" action="movie.php">
        <label>Movie Title:</label>
        <input type="text" name="title" maxlength="255"/>
        <label>Movie Category:</label>
        <input type="text" name="category" maxlength="255"/>
        <label>Movie Length (in minutes):</label>
        <input type="number" name="length" min="1" max="400"/>
        <input type="submit" name="addItem" value="Submit"/>
    </form>
    <form method="POST" id="allGone" action="movie.php">
        <input type="submit" name="deleteAll" value="Delete All Inventory"/>
    </form>
    <table border="1" id="videos">
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Category</th>
            <th>Length</th>
            <th>Rented</th>
        </tr>
    </table>
    

</html>


<?php 
error_reporting(E_ALL);
ini_set('display_errors', 1);
$dbhost = 'oniddb.cws.oregonstate.edu';
$dbname = 'catesia-db';
$dbuser = 'catesia-db';
$dbpass = 'lDQEFSNyQixMYm7E';

//Done with the help of php mysqli documentation
$connect = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($connect->connect_errno) {
    echo "Failed to connect: (" . $connect->connect_errno . ") " . $connect->connect_error;
}


//THis part of the code was written with great help from the w3 page
if(isset($_POST['delete'])) {
    
    $sql = "DELETE FROM videoInventory WHERE d =".$_POST['index'];  
    mysqli_query($connect,$sql);
   
}
if(isset($_POST['checkout'])) {
    $value = 1;
    $result = mysqli_query($connect, "SELECT * FROM videoInventory WHERE rented = '1'");
    while($row = $result->fetch_assoc()){
        if($row['d'] == $_POST['index']) {
            $value = 0;
        }
    }
    
    $sql = "UPDATE videoInventory SET rented = '$value' WHERE d =".$_POST['index'];
    mysqli_query($connect,$sql);
    
    }

if(isset($_POST["deleteAll"])) {
    $sql = "Truncate TABLE videoInventory";
    mysqli_query($connect,$sql);
}

if(isset($_POST["addItem"])) {
    if(!isset($_POST["title"])) {
        echo "Need Title";   
    }
    
    
    else {
       
        $title = $_POST["title"];
        $category = $_POST["category"];
        $length = $_POST["length"];
        $rented = 1;
        
        if(!$category) {
	    $newCategory = NULL;
        } 
        if(!$length) {
	    $newLength = NULL;
        }
        
        if (!($stmt = $connect->prepare("INSERT INTO videoInventory(name,category,length,rented) VALUES (?,?,?,?)"))) {
            echo "Prepare failed";
        }

        if (!$stmt->bind_param("ssii", $title,$category,$length,$rented)) {
            echo "Binding parameters failed";
        }
        if (!$stmt->execute()) {
            echo "Execute failed";
        }
        
    }
    
    
    
}

$filterCategories = mysqli_query($connect,"SELECT * FROM videoInventory");
echo '<form action="movie.php" method="POST">';

$categoryArray = array();
echo '<select name="toFilter">';
while($selection = mysqli_fetch_array($filterCategories)) {
	if(!in_array($selection['category'], $categoryArray)) {
		if($selection['category'] != NULL) {
		array_push($categoryArray, $selection['category']);
		echo "<option value='".$selection['category']."'>".$selection['category']."</option>";
		}	
	}
}
if(!empty($categoryArray)) {
	echo '<option value="all">All</option>';
}
echo '<input type = "submit" value="Filter Results">';
echo '</select>';
echo '</form>';

$filter = 'all';
$getAll = mysqli_query($connect,"SELECT * FROM videoInventory");
if(isset($_POST['toFilter'])) {
    $filter = $_POST['toFilter'];
}
if(($filter == 'all')) {
	while($row = mysqli_fetch_array($getAll)) {
		echo "<tr>";
		echo "<td>".$row['d']."   </td>";
		echo "<td>".$row['name']."   </td>";
		echo "<td>".$row['category']."   </td>";
		echo "<td>".$row['length']."   </td>";
        if($row['rented'] == 1) {
            echo "<td>"."Available"."   </td>";
        }
         else {
            echo "<td>"."Checked out"."   </td>";   
            }
		
		echo "<td><form method=\"POST\" action=\"movie.php\">";
		echo "<input type=\"hidden\" name=\"index\" value=\"".$row['d']."\">";
		echo "<input type=\"submit\" value=\"delete\" name=\"delete\" >";
		echo "</form>";
		echo "<td><form method=\"POST\" action=\"movie.php\">";
		echo "<input type=\"hidden\" name=\"index\" value=\"".$row['d']."\">";
		echo "<input type=\"submit\" value=\"CheckIn/Checkout\" name=\"checkout\">";
		echo "</form>";
		echo "</tr>";
		}
}

else {
    while($row = mysqli_fetch_array($getAll)) {
        if($row['category'] == $filter){
		echo "<tr>";
		echo "<td>".$row['d']."   </td>";
		echo "<td>".$row['name']."   </td>";
		echo "<td>".$row['category']."   </td>";
		echo "<td>".$row['length']."   </td>";
        if($row['rented'] == 1) {
            echo "<td>"."Available"."   </td>";
        }
         else {
            echo "<td>"."Checked out"."   </td>";   
            }
		
		echo "<td><form method=\"POST\" action=\"movie.php\">";
		echo "<input type=\"hidden\" name=\"index\" value=\"".$row['d']."\">";
		echo "<input type=\"submit\" value=\"delete\" name=\"delete\" >";
		echo "</form>";
		echo "<td><form method=\"POST\" action=\"movie.php\">";
		echo "<input type=\"hidden\" name=\"index\" value=\"".$row['d']."\">";
		echo "<input type=\"submit\" value=\"CheckIn/Checkout\" name=\"checkout\">";
		echo "</form>";
		echo "</tr>";
        }
    }
    
    
    
}



    mysqli_close($connect);
?>





