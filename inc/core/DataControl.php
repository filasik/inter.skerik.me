<?php

class DataControl
{
    /**
     * @param string $cond
     */
    public $table;

    function CRUD($cond = null)
    {
        global $table;
        global $primaryCol;
        global $cols;
        global $colAs;
        global $inputTypes;
        global $page;
        global $identifiers;
        global $dropdownItems;

        if (isset($_GET['edit'])) {
            $this->editData($table, $primaryCol, $_GET['edit'], $cols, $colAs, $inputTypes, $page);
        } elseif (isset($_GET['remove'])) {
            $this->deleteData($table, $primaryCol, $_GET['remove'], $page);
        } elseif (isset($_GET['new'])) {
            $this->createData($table, $cols, $colAs, $inputTypes, $page, $dropdownItems);
        } elseif (isset($_GET['view'])) {
            $this->viewData($table, $primaryCol, $_GET['view'], $cols, $colAs, $page);
        } else {
            $this->selectData($table, $identifiers, $primaryCol, $cond);
        }
    }

    //zobrazuje data

    function editData($table, $idCol, $id, $cols, $colsAs, $inputTypes, $page)
    {
        global $database;
        global $doajob;
        global $dropdownItems;
        $item = $database->queryOne("select * from $table where $idCol=$id");

        echo "<form method='post' enctype='multipart/form-data'>";
        echo "<tr><td colspan='2'><input type='submit' name='save' class='btn btn-success' value='Uložit'></td></tr>";
        for ($i = 0; $i < count($cols); $i++) {
            $col = $cols[$i];
            if ($inputTypes[$i] === "textarea") {
                $input = "<textarea class='form-control editor' name='$cols[$i]' placeholder='$colsAs[$i]'>$item[$col]</textarea>";
            } elseif ($inputTypes[$i] === "text") {
                $input = "<input type='$inputTypes[$i]' class='form-control' name='$cols[$i]' placeholder='$colsAs[$i]' value='$item[$col]'>";
            } elseif ($inputTypes[$i] == "dropdown") {
                $input = "<select name='$col' id='$col' class='form-control'>";
                $selected = $database->queryOne("select $col from $table where $idCol='$id'")[$col];
                foreach ((array)$dropdownItems as $dropdownItem) {
                    //ověření, pokud je vybraný item z databáze v seznamu možností, pokud ano, přiřadí ji selected
                    ($selected == $dropdownItem) ? $selection = "selected" : $selection = null;
                    $input .= "<option value='$dropdownItem' $selection>$dropdownItem</option>";
                }
                $input .= "</select>";
            }
            echo "
            <tr>
                <td>$colsAs[$i]</td>
                <td>$input</td>
            </tr>
            ";
        }
        echo "<tr><td colspan='2'><input type='submit' name='save' class='btn btn-success' value='Uložit'></td></tr></form>";
        //todo save
        if (isset($_POST['save'])) {
            $query = "UPDATE `$table` SET ";
            foreach ($cols as $col) {
                if ($col == end($cols)) {
                    $query .= "`$col`='$_POST[$col]' ";
                } else {
                    $query .= "`$col`='$_POST[$col]', ";
                }
            }
            $query .= "WHERE `$idCol`='$id'";
            $database->query($query);
            $doajob->redirect("p=$page&alert=updated");
        }
    }

    //edituje data

    function deleteData($table, $idCol, $id, $page)
    {
        global $database;
        global $doajob;
        $database->query("delete from $table where $idCol=$id");
        $doajob->redirect("p=$page&alert=removed");
    }

    //vytvoři data

    function createData($table, $cols, $colsAs, $inputTypes, $page, $dropdownItems)
    {
        global $database;
        global $doajob;
        global $dropdownItems;


        global $_FILES;
        $input = null;
        echo "<form method='post' enctype='multipart/form-data'>";
        for ($i = 0; $i < count($cols); $i++) {
            if ($inputTypes[$i] == "textarea") {
                $input = "<textarea class='form-control editor' name='$cols[$i]' placeholder='$colsAs[$i]'></textarea>";
            } elseif ($inputTypes[$i] == "text") {
                $input = "<input type='$inputTypes[$i]' class='form-control' name='$cols[$i]' placeholder='$colsAs[$i]'>";
            } elseif ($inputTypes[$i] == "dropdown") {
                $input = "<select name='$cols[$i]' class='form-control selectpicker' data-live-search='true'>";
                foreach ((array)$dropdownItems as $dropdownItem) {
                    $input .= "<option value='$dropdownItem'>$dropdownItem</option>";
                }
                $input .= "</select>";
            }
            echo "
            <tr>
                <td>$colsAs[$i]</td>
                <td>$input</td>
            </tr>
            ";
        }
        echo "<tr><td colspan='2'><input type='checkbox' name='addMore' id='checkbox_more'><label for='checkbox_more' class='mr-3'>&nbsp;Vložit další</label><input type='submit' name='add' class='btn btn-success' value='Vložit záznam'></td></tr></form>";
        if (isset($_POST['add'])) {
            $query = "INSERT INTO `$table` (";
            foreach ($cols as $col) {
                if ($col == end($cols)) {
                    $query .= "`$col`";
                } else {
                    $query .= "`$col`, ";
                }
            }
            $query .= ") VALUES (";
            foreach ($cols as $col) {
                if ($col == end($cols)) {
                    $query .= "'$_POST[$col]'";
                } else {
                    $query .= "'$_POST[$col]', ";
                }
            }
            $query .= ");";
            $database->query($query);
            if (isset($_POST['addMore'])) {
                $doajob->redirect("p=$page&new&alert=inserted");
            } else {
                $doajob->redirect("p=$page&alert=inserted");
            }
        }
    }

    //smaže data

    function viewData($table, $idCol, $id, $cols, $colAs, $page)
    {
        global $database;
        $item = $database->queryOne("select * from $table where $idCol=$id");

        echo "<tr><td colspan='2'><a href='?p=$page'>Zpět</a></td></tr>";
        for ($i = 0; $i < count($cols); $i++) {
            $col = $cols[$i];
            echo "
            <tr>
                <td>$colAs[$i]</td>
                <td>$item[$col]</td>
            </tr>
            ";
        }
        echo "<tr><td colspan='2'><a href='?p=$page'>Zpět</a></td></tr>";
    }

    //vypíše vše

    function selectData($table, $identifiers, $primaryCol, $cond)
    {
        global $database;
        $colSpan = count($identifiers) + 1;
        //echo "<tr><td colspan='$colSpan'><a href='?$_SERVER[QUERY_STRING]&new' class='btn btn-success'>Nový záznam</a></td></tr>";
        foreach ($database->queryAll("select * from $table $cond") as $item) {
            echo "<tr>";
            foreach ($identifiers as $identifier) {
                echo "<td>" . substr($item[$identifier], 0, 70) . "</td>";
            }
            echo "
                <td class='text-right'>
                    <a href='?$_SERVER[QUERY_STRING]&view=$item[$primaryCol]' class='btn btn-sm btn-primary'><i class='fas fa-eye'></i></a>
                    <a href='?$_SERVER[QUERY_STRING]&edit=$item[$primaryCol]' class='btn btn-sm btn-info'><i class='fas fa-edit'></i></a>
                    <a href='?$_SERVER[QUERY_STRING]&remove=$item[$primaryCol]' class='btn btn-sm btn-danger'><i class='fas fa-trash-alt'></i></a>
                </td>
            </tr>
            ";
        }
    }
}