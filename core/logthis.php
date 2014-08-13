<style>
    .entry{
        float: left;
        width: 100%;
        clear: both;
        padding:5px;
        display: inline;
        
    }
    
    .entry .key{
        float: left;
        min-width:200px;
        color: #0063DC;
        padding-right: 20px;
        display: inline;
    }
    .entry .val{
        float: left;
        width:700px;
        display: inline;
    }    
</style>
<?php
session_start();
if (!isset($_SESSION['LOG_THIS'])){
    return;
}
foreach($_SESSION['LOG_THIS'] as $key=>$val){ ?>
<div class="entry">
    <div class="key"><?=$key?></div>
    <div class="val"><?=$val?></div>
</div>

<?php
}

?>
