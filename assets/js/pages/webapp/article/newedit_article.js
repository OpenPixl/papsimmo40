import axios from 'axios';
import * as bootstrap from 'bootstrap';
import {toasterMessage} from "../../../components/bootstrap/toaster";
import {
    formatDate,
    useTomSelect,
    initializeTinyMCE
} from "../../../components/appli/common";

export function initNewEditArticlesPage(){
    initializeTinyMCE(500);
}