<?php
header("Content-type: application/javascript");
?>
<script>
var HOSTURL = HOSTURL || (function(){
    var _args = {}; // private

    return {
        init : function(Args) {
            _args = Args;
            // some other initialising
        },
        helloWorld : function(i) {
            return _args[i];
        }
    };
}());
HOSTURL.init([<?php echo json_encode($url_host); ?>]);
//var ur = <?php //echo json_encode($url_host); ?>;
</script>
 <script type="text/javascript" src="<?=$this->url('home')?>js/new_work_js.php"></script>