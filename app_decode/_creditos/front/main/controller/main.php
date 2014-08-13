<?php

class main extends main_controller {

    function main() {
        $this->mod = $this->model("formalta_model");
    }

    function init($id = 0) {
        $this->mod->initClassModel($id);
        //etapas
    }

    

}
