<?php
define('MASTER_USER','master');

function printBS(){
    echo'<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
  integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
  integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
  integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
  integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<html dir="rtl">
<div align="center">';
}




function admin__shabatMenu(){

}
function admin__blockAndFreeUsers(){
    if (isset($_POST["phone"])){
        $phone=$_POST["phone"];
        //select yashivaID from user where phone=&phone
        //$t=select * from users where yeshivaID=yeshivaID
        $t=[$phone];
        $users = json_decode(file_get_contents(__DIR__ . "/data/blockedUsers.json"), true);
        if (!empty($t)){
            if (in_array($phone,$users)){
                unset($users[array_search($phone,$users)]);
            file_put_contents(__DIR__ . "/data/blockedUsers.json", json_encode($users));
            $_SESSION["YE_UPDATE_Mes"]="המשתמש שוחרר בהצלחה";
            }
            else{
            array_push($users,$phone);
            file_put_contents(__DIR__ . "/data/blockedUsers.json", json_encode($users));
            $_SESSION["YE_UPDATE_Mes"]="המשתמש נחסם בהצלחה";
        }
        }
        else {
            $_SESSION["YE_UPDATE_Mes"]="המשתמש אינו קיים";
        }
        headerHome();
    }
    
    else{
    echo '<div align="center" dir="rtl">
        <!--a onclick="addButton()"><button>הוסף משתמש</button></a-->
        <br><br>
        <form method="POST">
            <div id="input-container" dir="rtl"></div>
            <input name="phone" type="number" placeholder="מספר טלפון">
            <br><br>
            <button type="submit">עדכן</button>
        </form>
        </div>
        
        <script>
        //const inputContainer = document.getElementById("input-container");
            
        //     // function addButton() { 
        //     //     var myParent = inputContainer;
        //     //     myParent.innerHTML+=`<input type="number" placeholder="מספר טלפון">
        //     //     <br><br>`;
        //     // }
        </script>'
        ;}
}
function admin__sendMessageToUser(){
    global $from;
    global $messageId;
    
    if(isset($_POST['send-message-one-user'], $_POST['phone']) && is_string($_POST['send-message-one-user']) && is_string($_POST['phone']) && !empty($_POST['send-message-one-user']) && !empty($_POST['phone'])){
        $adminPanelMesColor = "darkturquoise";

            $from = $_POST['phone'];
            $messageId = "adminPanel_send_one_message__" . uniqid();
            //sendMessage($_POST['phone'], "text", $_POST['send-message-one-user']);
            $adminPanelMes = "במידה והמשתמש פעיל בבוט, ההודעה נשלחה בהצלחה";
        
        if(isset($_GET['close-after-send'])){
            echo "<script>alert('" . $adminPanelMes . "');window.close();</script>";
        }
        elseif(isset($_GET['return-to-messages-log'])){
            header("Location: ?act=user-messages-log&phone=" . $from);
        }
        else{
            $_SESSION['YE_BM_UPDATE_Mes'] = "<h2 style='color:" . $adminPanelMesColor . ";'>" . $adminPanelMes . "</h2>";
            headerHome();
        }
    }
    else{
        $title = "שליחת הודעה למשתמש";
        
        $phone = "";
        $name = "";
        
        if(isset($_GET['phone']) && !empty(intval($_GET['phone'])) && strlen(intval($_GET['phone'])) == 12)
            $phone = htmlspecialchars($_GET['phone']);
            
        if(isset($_GET['name']) && !empty($_GET['name']) && mb_strlen($_GET['name']) > 4 && mb_strlen($_GET['name']) < 30)
            $name = htmlspecialchars($_GET['name']);
        
        echo '
        <div align=center>
        <form method="POST">
            מספר טלפון: 
            <input name="phone" dir="ltr" value="' . $phone . '" required/>
            <br><br>
            לשלוח כתבנית? <input type="checkbox" name="send-as-template" onclick="onCheckTemplate();"/>
            <br><br>
            <div id="template-name-div">
                לטובת התבנית, יש להזין את שם הנמען <input type="text" value="' . $name . '" name="send-as-template-name"/>
            </div>
            <br><br>
            <div id="content-div"></div>
            <script>
                function onCheckTemplate(){
                    var templateCheck = document.getElementsByName("send-as-template")[0];
                    var contentDiv = document.getElementById("content-div");
                    var templateName = document.getElementsByName("send-as-template-name")[0];
                    var templateNameDiv = document.getElementById("template-name-div");
                    if(templateCheck.checked){
                        contentDiv.innerHTML = \'תוכן התבנית: <input name="send-message-one-user" style="width: 500px;" required>\';
                        templateName.setAttribute("required", "required");
                        templateNameDiv.style.display = "block";
                    }
                    else{
                        contentDiv.innerHTML = \'תוכן ההודעה: <br><textarea rows="15" cols="35" name="send-message-one-user"></textarea>\';
                        templateName.removeAttribute("required");
                        templateNameDiv.style.display = "none";
                    }
                }
                onCheckTemplate();
            </script>
            <br><br>
            <button type="submit">שלח עכשיו</button>
        </form></div>';
    }
}
function admin__updateContacts(){
    //$link=getContactsLink(yeshivaID);
    $link='https://google.com';
    $count=3;

    if(isset($_POST["update-contacts-request"])){
        //initSheets();
        //updateAllContactsFromGoogleSheets();
        
        //$count = getCountOfContactsUsers();
        
        $_SESSION['YE_UPDATE_Mes'] = "<h2 style='color:darkturquoise;'>" . $count . " אנשי הקשר עודכנו מהגוגל שיטס בהצלחה! תבורך 😘</h2>";
    
        headerHome();
    }
    else{
        //$yID=select yeshivaID from users where phone=$_SESSION['YE_UPDATE_User']['Logged'];
        //$count=select count(id) from users where yeshivaId=
        echo '<div align="center">
        <form method="POST">
            <h2>מספר המשתמשים הקיים '.$count.'</h2>
            <button name="update-contacts-request">עדכן</button>
            <br><br>
            <a href='.$link.' target="_blank">קובץ המשתמשים</a>
            </form>
            </div>
        ';
    }
}
function admin__updateShiftsTable(){}
function admin__updateGuardsTable(){}
function admin__sendMessageToAllUsers(){
    if(isset($_POST['txt'])){}
    else{
        printBS();
        echo '<form><br>
        <div>
            <button >שלח</button><br><br>
            <textarea cols="50" rows="10" name="txt"></textarea>
            </div>
            </form>
        ';
    }
}
function admin__sendSpecialRegister(){}
function admin__printLastChatsHtml(){}
function admin__printLogMessagesUserHtml(){}
function admin__printJson(){}
function admin__uploadPhotos(){
    if(isset($_POST['upload']) && isset($_POST['img'])){
        foreach ($_POST['img'] as $img) {
            move_uploaded_file($_FILES["img"]["tmp_name"], 
            basename($_FILES["img"]["name"]));
        }
    }
    
    else{
        printBS();
        echo '<form method="POST"><br>
        <button class="btn btn-success" name="upload">עדכן</button>
        <br>
        <br>
        <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">שם</th>
            <th scope="col">טלפון</th>
            <th scope="col">תמונה</th>
            </tr>
        </thead>
        <tbody>';
        $i=0;
        $users = json_decode(file_get_contents(__DIR__ . "/data/users.json"), true);
        foreach ($users as $key=>$value) {
            $user=array($key=>$value);
            echo'
                <tr>
                <th scope="row">'.$i.'</th>
                <td>'.$value.'</td>
                <td>'.$key.'</td>
                <td>
                    <label for="img">בחר קובץ</label>
                    <input type="file" id="img" name="img['.$i.']" accept="image/*">
                </td>
                </tr>
            ';
            $i++;
        }
        echo '</tbody></table></form>';
        
    //     <label for="img">בחר קובץ</label>
    //     <input type="file" id="img" name="img" accept="image/*">
    //     <button type="submit">עדכן</button>
    //   </form>
    //   </div>;
    //   </html>';
    }
}
function admin__printImagesTable(){}
function admin__deletePhoto(){}
function admin__manageUsers(){
    if (isset($_POST["phone"])){
        $phone1=$_POST["phone"];
        //$name=select name from users where phone=$phone
        $name='###';
        $phone=array($phone1=>$name);
        //select yashivaID from user where phone=&phone
        //$t=select * from users where yeshivaID=yeshivaID
        $t=[$phone];
        $users = json_decode(file_get_contents(__DIR__ . "/data/users.json"), true);
        if (!empty($t)){
            if (isset($users[$phone1])){
                $_SESSION["YE_UPDATE_Mes"]="המשתמש הינו מנהל, לא ניתן להסיר מנהלים בעלי הרשאות";
                $perm=json_decode(file_get_contents(__DIR__ . "/data/permissions.json"), true);
                $flag=false;
                foreach ($perm as $i){
                    if (in_array($phone1,$i)){
                        $flag=true;
                        break;
                    }
                }
                if (!$flag){
                    unset($users[$phone1]);
                    $_SESSION["YE_UPDATE_Mes"]='המשתמש הוסר מניהול';
                }
                file_put_contents(__DIR__ . "/data/users.json", json_encode($users));
            }
            else{
            $users=$users+$phone;
            file_put_contents(__DIR__ . "/data/users.json", json_encode($users));
            $_SESSION["YE_UPDATE_Mes"]=(string)($users).'\n\n'.$phone[$phone1]."המשתמש הוגדר כמנהל בהצלחה";
        }
        }
        else {
            $_SESSION["YE_UPDATE_Mes"]="המשתמש אינו קיים";
        }
        headerHome();
    }

    else{
        echo '<div align="center" dir="rtl">
            <br><br>
            <form method="POST">
                <div id="input-container" dir="rtl"></div>
                <input name="phone" type="number" placeholder="מספר טלפון">
                <br><br>
                <button type="submit">עדכן</button>
            </form>
            </div>';
    }
}
function admin__manageUsersPermissions(){
    global $options;
    global $users;
    
    if(isset($_POST['update-users-permissions-post']) && isset($_POST['permissions']) && is_array($_POST['permissions'])){
        $newPermissions = array();
        
        // Set all options in the arr.
        foreach($_POST['permissions'] as $user => $userPermissions){
            foreach ($userPermissions as $permission => $mode){
                $newPermissions[$permission] = array();
            }
            break;
        }
        
        // Fill the options by users.
        foreach($_POST['permissions'] as $user => $userPermissions){
            foreach ($userPermissions as $permission => $mode){
                if($mode === "1"){
                    $newPermissions[$permission][] = $user;
                }
            }
        }
        
        file_put_contents(__DIR__ . "/data/permissions.json", json_encode($newPermissions));
        
        $_SESSION['YE_UPDATE_Mes'] = "<h2 style='color:darkturquoise;'>עדכון ההרשאות בוצע בהצלחה! שכוייעח 😘</h2>";
        headerHome();
    }
    else{
        $users = json_decode(file_get_contents(__DIR__ . "/data/users.json"), true);
        echo '
        <div align="center" dir="rtl">
            ניהול הרשאות משתמשים
            <style>.yes{color:black;background-color:limegreen;font-weight:bold;} .no{color:black;background-color:lightcoral;}</style>
            <script>
                function changeClass(select){
                    if(select.value == "1"){
                        select.setAttribute("class", "yes");
                    }
                    else{
                        select.setAttribute("class", "no");
                    }
                }
            </script>
            <form method="POST">
            <table border=1>';
        
            echo "\n\t\t\t\t\t" . '<tr>';
            echo '<th>שם הרשאה</th>';
            foreach ($users as $user => $notInUse){
                if ($users[$user] == MASTER_USER) continue;
                
                echo '<th>' . htmlspecialchars($users[$user]) . '</th>';
            }
            echo '</tr>';
            
            foreach ($options as $optionName => $allowUsers){
                echo "\n\t\t\t\t\t" . '<tr>';
                echo '<td>' . htmlspecialchars($optionName) . '</td>';
                foreach ($users as $user => $notInUse_2){
                    if ($users[$user] == MASTER_USER) continue;
                    
                    echo '<td>';
                    echo '<select onchange="changeClass(this)" class="' . (in_array($user, $allowUsers) ? "yes" : "no") . '" name="permissions[' . htmlspecialchars($user) . '][' . htmlspecialchars($optionName) . ']">';
                    echo '<option class="yes" value="1" ' . (in_array($user, $allowUsers) ? "selected" : "") . '>כן</option><option class="no" value="0" ' . (in_array($user, $allowUsers) ? "" : "selected") . '>לא</option>';
                    echo '</select>';
                    echo '</td>';
                }
                echo '</tr>';
            }
        
        echo '
            </table>
            <br><br>
            <button type="submit" name="update-users-permissions-post">עדכן</button>
        </form>
        </div>
        ';
    }
}

function admin__settings(){
    printBS();
    echo '
        <form name="POST">
            <div align="center"><br>
                <button name="update">עדכן</button><br><br>
                <input placeholder="קישור לרישום שבתות"><br><br>
                תמונת הישיבה
                <input type="file" name="logo" accept="image/*"><br><br>
                <input placeholder="קישור לרישום שמירות"><br><br>';
    //$commands=getCommands(yeshivaID);
    $commands=array("zr"=>"mjgdhbvcgnhdmj,jmnbv");
    foreach ($commands as $key => $value) {
        echo '<div>
        <button type="button" onclick="delLine()">הסר</button>
        <input value="שם פקודה">
        <input value="'.htmlspecialchars($key).'">
        <input value="תיאור פקודה">
        <input value="'.htmlspecialchars($value).'" width="50" height="300">
        <br><br>
        </div>';
    }
    echo'
                <div id="lineContainer"> </div>
                <button onclick="addLine()" type="button">הוסף פקודה</button>
                <script>
                    const lineContainer = document.getElementById("lineContainer");
                    function addLine() {
                        lineContainer.innerHTML += ` <div>
                            <button type="button" onclick="delLine()">הסר</button>
                            <input placeholder="שם פקודה">
                            <input placeholder="קוד פקודה">
                            <input placeholder="תיאור פקודה">
                            <input placeholder="טקסט פקודה" width="50" height="300">
                            <br><br>
                            </div>
                            `;
                    }
                </script>
            </div>
        </form>

        </html>';
}
?>