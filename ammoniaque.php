<?php
/**
 * @package Ammoniaque
 * @version 1.0.0
 */
/*
Plugin Name: Ammoniaque
Plugin URI: http://ammoniaque.io/
Description: Retrouvez les fonctions d'Ammoniaque.io directement dans votre interface d'administration Wordpress
Author: Ammoniaque.io
Version: 1.0.0
*/

//sanitization and validation of email update field
if(!empty($_POST['ammoniaque_email'])){
    //sanitization
    $sanitized_email = sanitize_email($_POST['ammoniaque_email']);
    //validation
    if(filter_var($sanitized_email, FILTER_VALIDATE_EMAIL)) {
        $body = array(
            "type" => "changeEmail",
            "newEmail" => $sanitized_email,
            "email" => get_option('admin_email'),
            "websiteUrl" => get_option('siteurl')
        );
        aiowp_postData($body);
    }
}

//sanitization and validation of new request fields
if(isset($_POST['ammoniaque_newrequest_deadline']) && !empty($_POST['ammoniaque_newrequest_title']) && !empty($_POST['ammoniaque_newrequest_content'])){
    //sanitization
    $ammoniaque_newrequest_deadline = sanitize_text_field($_POST['ammoniaque_newrequest_deadline']);
    $sanitized_ammoniaque_newrequest_title = sanitize_text_field($_POST['ammoniaque_newrequest_title']);
    $sanitized_ammoniaque_newrequest_content = sanitize_text_field($_POST['ammoniaque_newrequest_content']);
    //validation
    if(
        ($ammoniaque_newrequest_deadline === 'express' ||
        $ammoniaque_newrequest_deadline === '24h' || 
        $ammoniaque_newrequest_deadline === '48h' || 
        $ammoniaque_newrequest_deadline === '72h') &&
        is_string($sanitized_ammoniaque_newrequest_title) &&
        is_string($sanitized_ammoniaque_newrequest_content)
    ){
        $body = array(
            "type" => "newRequest",
            "email" => get_option('admin_email'),
            "websiteUrl" => get_option('siteurl'),
            "deadline" => $ammoniaque_newrequest_deadline,
            "requestTitle" => $sanitized_ammoniaque_newrequest_title,
            "requestContent" => $sanitized_ammoniaque_newrequest_content
        );
        aiowp_postData($body);        
    }
}

//sanitization and validation of contact fields
if(isset($_POST['ammoniaque_contact_deadline']) && !empty($_POST['ammoniaque_contact'])){
    //sanitization
    $ammoniaque_contact_deadline = sanitize_text_field($_POST['ammoniaque_contact_deadline']);
    $sanitized_ammoniaque_contact = sanitize_text_field($_POST['ammoniaque_contact']);
    //validation
    if( 
        ($ammoniaque_contact_deadline === 'non-urgent' || 
        $ammoniaque_contact_deadline === 'urgent') && 
        is_string($sanitized_ammoniaque_contact)
    ){
        $body = array(
            "type" => "contact",
            "email" => get_option('admin_email'),
            "websiteUrl" => get_option('siteurl'),
            "deadline" => $ammoniaque_contact_deadline,
            "message" => $sanitized_ammoniaque_contact
        );
        aiowp_postData($body);        
    }
}

function aiowp_postData($body)
{
    $args = array(
        'body' => $body
    );
     
    $response = wp_remote_post('https://us-central1-ammoniaque-app.cloudfunctions.net/postDataFromCms', $args);
}

function aiowp_backend_page()
{
    add_menu_page(
        'Ammoniaque', 
        'Ammoniaque', 
        'manage_options', 
        'ammoniaque', 
        'aiowp_show_ammoniaque_dashboard', 
        'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDIyLjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkNhbHF1ZV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIKCSB2aWV3Qm94PSIwIDAgMzkxLjUgMzkxLjYiIHN0eWxlPSJlbmFibGUtYmFja2dyb3VuZDpuZXcgMCAwIDM5MS41IDM5MS42OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+Cgkuc3Qwe29wYWNpdHk6MDt9Cgkuc3Qxe2ZpbGw6I0ZBRjhGMjt9Cgkuc3Qye2ZpbGw6IzlFQTNBODt9Cjwvc3R5bGU+CjxnIGlkPSJDYWxxdWVfMV8xXyIgY2xhc3M9InN0MCI+Cgk8cGF0aCBjbGFzcz0ic3QxIiBkPSJNLTU3LjUtNTUuM3Y1MDBoNTAwdi01MDBILTU3LjV6IE0zMTMuNywzOTEuMkg3MC45di0yNS43aDI0Mi44VjM5MS4yeiIvPgo8L2c+CjxnIGlkPSJDYWxxdWVfMl8xXyI+Cgk8cG9seWdvbiBjbGFzcz0ic3QyIiBwb2ludHM9IjE4NS4zLDAgMTMwLjIsMTE0LjcgMjIyLjEsNzYuNCAJIi8+Cgk8cG9seWdvbiBjbGFzcz0ic3QyIiBwb2ludHM9IjIyNy44LDc5LjggMTM1LjgsMTE4LjEgOTMuNiwyMDUuOSAzMTQuNywyNjAuNyAJIi8+Cgk8cG9seWdvbiBjbGFzcz0ic3QyIiBwb2ludHM9IjcwLjksMzY1LjUgMTkzLDM2NS41IDMyMy4zLDMwMS4zIDMwNi43LDI2Ni43IDg1LjYsMjExLjkgMCwzODkuOSA3MC45LDM4OS45IAkiLz4KCTxwb2x5Z29uIGNsYXNzPSJzdDIiIHBvaW50cz0iMzEzLjcsMzY1LjUgMzEzLjcsMzg5LjUgMzg0LjIsMzg5LjUgMzQxLjYsMzAwLjkgMjEwLjUsMzY1LjUgCSIvPgo8L2c+Cjwvc3ZnPgo='
    );
}

function aiowp_show_ammoniaque_dashboard()
{
    echo "
        <style>
            textarea {
                width: 100%;
            }

            #block_container {
                display: flex;
            }    

            .bg_transparent {
                border-color: darkgrey;
                margin-top: 1.5em;
            }

            .bg_white {
                background-color: white;
                border-color: darkgrey;
                padding: 1em;
                margin-top: 1em;
                margin-right: 1.5em;
            }

            .half_bg_white {
                width: 50%;
                background-color: white;
                border-color: darkgrey;
                padding: 1em;
                margin-top: 1em;
                margin-right: 1.5em;
            }

            .bg_error {
                background-color: #F9E2E2;
                border-color: darkgrey;
                padding: 1em;
                margin-top: 1em;
                margin-right: 1.5em;
            }

            .bg_success {
                background-color: #EFF9F1;
                border-color: darkgrey;
                padding: 1em;
                margin-top: 1em;
                margin-right: 1.5em;
            }

            .main_title {
                font-size: 1.6em;
                color: black;
            }

            .bold {
                font-weight: bold;
            }

            .send_btn {
                border-radius: 0.15em;
                border: 1px solid #0f729f;
                padding: 0.4em; 
                background-color: #f3f5f6;
                color: #0f729f;
            }
        </style>

        <div class='bg_transparent'>
            <span class='main_title'>Ammoniaque</span>
        </div>";

        //display success/error messages for email update field
        if(isset($_POST['ammoniaque_email'])){
            $sanitized_email = sanitize_email($_POST['ammoniaque_email']);
            if(filter_var($sanitized_email, FILTER_VALIDATE_EMAIL)) {
                echo "<div class='bg_success'>Email correctement modifié</div>";
            }
            else{
                echo "<div class='bg_error'>Email invalide. Le changement n'a pas été effectué</div>";
            }
        }

        //display success/error messages for new request fields
        if(isset($_POST['ammoniaque_newrequest_deadline']) && !empty($_POST['ammoniaque_newrequest_title']) && !empty($_POST['ammoniaque_newrequest_content'])){
            $ammoniaque_newrequest_deadline = sanitize_text_field($_POST['ammoniaque_newrequest_deadline']);
            $sanitized_ammoniaque_newrequest_title = sanitize_text_field($_POST['ammoniaque_newrequest_title']);
            $sanitized_ammoniaque_newrequest_content = sanitize_text_field($_POST['ammoniaque_newrequest_content']);
            if(
                ($ammoniaque_newrequest_deadline === 'express' ||
                $ammoniaque_newrequest_deadline === '24h' || 
                $ammoniaque_newrequest_deadline === '48h' || 
                $ammoniaque_newrequest_deadline === '72h') &&
                is_string($sanitized_ammoniaque_newrequest_title) &&
                is_string($sanitized_ammoniaque_newrequest_content)
            ){
                echo "<div class='bg_success'>Votre requête a été correctement transmise</div>";
            }
            else{
                echo "<div class='bg_error'>Votre requête n'a pas été transmise</div>";
            }
        }

        //display success/error messages for contact fields
        if(isset($_POST['ammoniaque_contact_deadline']) && !empty($_POST['ammoniaque_contact'])){
            $ammoniaque_contact_deadline = sanitize_text_field($_POST['ammoniaque_contact_deadline']);
            $sanitized_ammoniaque_contact = sanitize_text_field($_POST['ammoniaque_contact']);
            if( 
                ($ammoniaque_contact_deadline === 'non-urgent' || 
                $ammoniaque_contact_deadline === 'urgent') && 
                is_string($sanitized_ammoniaque_contact)
            ){
                echo "<div class='bg_success'>Votre message a été correctement envoyé</div>";
            }
            else{
                echo "<div class='bg_error'>Votre message n'a pas été envoyé</div>";
            }
        }  
        

    echo "
        <div class='bg_white'>
        <span class='main_title'>Email de notifications</span>
            <p>
                Par défaut, vous serez notifié(e) des erreurs rencontrées à l'adresse email suivante :
                <span class='bold'>";

                    echo get_option('admin_email');
        
    echo "
                </span>
            </p>

            <p>
                Si vous souhaitez êtes averti(e) à une autre adresse email, indiquez-le dans le champ ci-dessous :
            </p>

            <form action='' method='post'>
                <p>
                    <input type='text' name='ammoniaque_email' placeholder='Nouvelle adresse' style='width: 25%' />
                    <input type='submit' class='send_btn' value='Modifier mon email' />
                </p>
            </form>
        </div>

        <div id='block_container'>

            <div class='half_bg_white'>
                <span class='main_title'>Nouvelle relecture</span>
                <form action='' method='post'>
                    <p>
                        Dans quel délai souhaitez-vous obtenir la relecture ? 
                        <select name='ammoniaque_newrequest_deadline'>
                            <option value='express'>Express</option>
                            <option value='24h'>24h</option>
                            <option value='48h'>48h</option>
                            <option value='72h'>72h</option>
                        </select>
                    </p>

                    <p>
                        <input style='width: 100%' id='document_title' type='text' name='ammoniaque_newrequest_title' placeholder='Donnez un titre à votre document' />
                    </p>

                    <p>
                        <textarea id='newrequest' name='ammoniaque_newrequest_content' rows='5' placeholder='Copiez-collez le contenu de votre document'></textarea>
                    <p>

                    <p>
                        <input type='submit' class='send_btn' value='Envoyer ma requête' />
                    </p>                
                </form>
            </div> 

            <div class='half_bg_white'>
                <span class='main_title'>
                    Contacter l'équipe
                </span>
                <form action='' method='post'>
                    <p>
                        Le message est-il urgent ? 
                        <select name='ammoniaque_contact_deadline'>
                            <option value=non-urgent>Non</option>
                            <option value='urgent'>Oui</option>
                        </select>
                    </p>

                    <p>
                        <textarea id='contact' name='ammoniaque_contact' rows='5' placeholder='Faites-nous part de vos questions, remarques ou suggestions'></textarea>
                    </p>

                    <p>
                        <input type='submit' class='send_btn' value='Envoyer mon message' />
                    </p>                
                </form>
            </div>

        </div>

    ";
}

add_action('admin_menu', 'aiowp_backend_page');

function aiowp_post_unpublished( $new_status, $old_status, $post ) {
    if ( $old_status != 'publish'  &&  $new_status == 'publish' ) {
        $body = array(
            "type" => "alert",
            "email" => get_option('admin_email'),
            "url" => get_permalink($post)
        );
        aiowp_postData($body);
    }
}
add_action( 'transition_post_status', 'aiowp_post_unpublished', 10, 3 );
