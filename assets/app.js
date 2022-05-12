/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */
import * as bootstrap from 'bootstrap';
// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import { startStimulusApp } from '@symfony/stimulus-bridge';

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.(j|t)sx?$/
));

import { Tooltip, Toast, Popover } from 'bootstrap';
// start the Stimulus application
import './bootstrap';

const $ = require('jquery');
//for less or more post in home
$(function () {
    $("div.posts").slice(6).hide();
    if ($("div.posts").length < 7) {
        $("#loadMoreproduit").hide();
        $("#loadLessproduit").hide();
    }
    $("#loadLessproduit").hide();
    $("#loadMoreproduit").on('click', function(e){
        e.preventDefault();
        $("div.posts:hidden").slice(0, 6).slideDown();
        $("#loadLessproduit").show();
        if ($("div.posts:hidden").length === 0) {
            $("#loadMoreproduit").hide();
            $("#loadLessproduit").show();
        }
    });
    $("#loadLessproduit").on('click', function(e){
        e.preventDefault();
        $("div.posts").slice(6, $("div.posts").length).hide();
        $("#loadLessproduit").hide();
        $("#loadMoreproduit").show();
    });
});


//for modal bootstrap
const container = document.getElementById("exampleModal");
const modal = new bootstrap.Modal(container);

document.getElementById("myInput").addEventListener("click", function () {
    modal.show();
});
document.getElementById("close").addEventListener("click", function () {
    modal.hide();
});
document.getElementById("closeFooter").addEventListener("click", function () {
    modal.hide();
});