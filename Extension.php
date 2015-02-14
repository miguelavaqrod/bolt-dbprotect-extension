<?php
// DB Protect Extension for Bolt

namespace Bolt\Extension\miguelavaqrod\dbprotect;

class Extension extends \Bolt\BaseExtension
{
    public function getName()
    {
        return "DatabaseProtect";
    }

    public function initialize()
    {
        $this->addTwigFunction('dbprotect', 'DBProtect');
        $this->addTwigFunction('dbform', 'DBForm');
    }
    
    /**
     * Check if we're currently allowed to view the page. If not, redirect to
     * the password page.
     *
     * @return \Twig_Markup
     */
    public function DBProtect()
    {

        if ($this->app['session']->get('dbprotect') == 1) {
            return new \Twig_Markup("<!-- DB Protection OK! -->", 'UTF-8');
        } else {

            $redirectto = $this->app['storage']->getContent($this->config['redirect'], array('returnsingle' => true));
            $returnto = $this->app['request']->getRequestUri();
            //simpleredirect($redirectto->link(). "?returnto=" . urlencode($returnto));
            $path = $redirectto->link(). "?returnto=" . urlencode($returnto);
            header("location: $path");
            echo "<noscript><p>Redirecting to <a href='$path'>$path</a>.</p></noscript>";
            echo "<script>window.setTimeout(function(){ window.location='$path'; }, 50);</script>";
        }
    }

    /**
     * Show the password form. If the visitor gives the correct username and password, they
     * are redirected to the page they came from, if any.
     *
     * @return \Twig_Markup
     */
    public function DBForm()
    {

        // Set up the form.
        $form = $this->app['form.factory']->createBuilder('form', $data)
                     ->add('username', 'text')
                     ->add('password', 'password')
                     ->getForm();

        if ($this->app['request']->getMethod() == 'POST') {

            $form->bind($this->app['request']);

            $data = $form->getData();

            if ($form->isValid()) {
                
                if($data['username'] != $this->config['username'] || $data['password'] != $this->config['password']){
                    
                    $link = mysqli_connect($this->config['dbhost'], $this->config['dbuser'] , $this->config['dbpass'], $this->config['dbname']);
                    $query = "SELECT id FROM ".$this->config['dbtable']." WHERE ".$this->config['dbfld1']." = '".$data['username']."' AND ".$this->config['dbfld2']." = '".$data['password']."'";
                    $ret = mysqli_query($link, $query);
                    if($ret && mysqli_num_rows($ret) > 0){
    
                        // Set the session var, so we're authenticated..
                        $this->app['session']->set('dbprotect', 1);
        
                        // Print a friendly message..
                        printf("<p class='message-correct'>%s</p>", $this->config['message_correct']);
        
                        $returnto = $this->app['request']->get('returnto');
        
                        // And back we go, to the page we originally came from..
                        if (!empty($returnto)) {
                            //simpleredirect($returnto);
                            $path = $returnto;
                            header("location: $path");
                            echo "<noscript><p>Redirecting to <a href='$path'>$path</a>.</p></noscript>";
                            echo "<script>window.setTimeout(function(){ window.location='$path'; }, 50);</script>";                    
                        }
       
                    }else{
    
                        // Remove the session var, so we can test 'logging off'..
                        $this->app['session']->set('dbprotect', 0);
        
                        // Print a friendly message..
                        printf("<p class='message-wrong'>%s</p>", $this->config['message_wrong']);
                        
                    }
                }else{
                        // Set the session var, so we're authenticated..
                        $this->app['session']->set('dbprotect', 1);
        
                        // Print a friendly message..
                        printf("<p class='message-correct'>%s</p>", $this->config['message_correct']);
        
                        $returnto = $this->app['request']->get('returnto');
        
                        // And back we go, to the page we originally came from..
                        if (!empty($returnto)) {
                            //simpleredirect($returnto);
                            $path = $returnto;
                            header("location: $path");
                            echo "<noscript><p>Redirecting to <a href='$path'>$path</a>.</p></noscript>";
                            echo "<script>window.setTimeout(function(){ window.location='$path'; }, 50);</script>";                    
                        }                    
                }

            } else {

                // Remove the session var, so we can test 'logging off'..
                $this->app['session']->set('dbprotect', 0);

                // Print a friendly message..
                printf("<p class='message-wrong'>%s</p>", $this->config['message_wrong']);
            }

        }

        // Render the form, and show it it the visitor.
        $this->app['twig.loader.filesystem']->addPath(__DIR__);
        $this->app['twig']->addGlobal("submit", $this->config['submit_text']);
        $html = $this->app['twig']->render('assets/passwordform.twig', array('form' => $form->createView()));

        return new \Twig_Markup($html, 'UTF-8');

    }

}
