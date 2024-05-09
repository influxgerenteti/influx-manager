<?php
namespace App\Helper;
use GuzzleHttp\Client;


class EmailAwsSender
{
    /**
     *
     * @var array
     */
    private $emails;

    /**
     *
     * @var array
     */
    private $cc;

    /**
     *
     * @var string
     */
    private $subject;


    /**
     *
     * @var string
     */
    private $message;

    /**
     *
     * @var string
     */
    private $template;
    //generic


    function __construct()
    {
        $this->template = 'generic';
        $this->emails =[];
        $this->cc =[];
        $this->subject ="Notificação Manager";
        $this->message = null;
        
       
    }
    
    
    function send(){
        
        try {
            $client = new Client();

            if($this->message == null){
                //menssagem não pode ser nula
                return false;
            }
            if($this->message == null){
                //emails não pode ser vazia
                return false;
            }
            $formParams = [];           
            
            $emailsStr = implode(",",$this->getEmails());
            $formParams['email'] = $emailsStr;
            $ccStr = implode(",",$this->getCc());
            $formParams['cc'] = $ccStr;

            $formParams['message'] =$this->getMessage();
            $formParams['subject'] =$this->getSubject();
            
            // Create a POST request
         
            $response = $client->request('POST', 'https://zctj09jfnh.execute-api.sa-east-1.amazonaws.com/production/sendmail/', [
                'body' =>  json_encode($formParams)
            ]);
            
            // Parse the response object, e.g. read the headers, body, etc.
            $headers = $response->getHeaders();
            $body = $response->getBody();
            if (str_contains($body, 'Erro')) {
                echo $body;
                return false;
            }
           
            // Output headers and body for debugging purposes
            // var_dump($headers, $body);
            return true;

        
            //code...
        } catch (\Throwable $th) {
            //throw $th;
             echo "Erro ao enviar email: " . $th->getMessage();
            return false;
        }
    }

    function sendAsync(){
        
    }


    /**
     * Get the value of emails
     *
     * @return  array
     */ 
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set the value of emails
     *
     * @param  array  $emails
     *
     * @return  self
     */ 
    public function setEmails(array $emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get the value of cc
     *
     * @return  array
     */ 
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Set the value of cc
     *
     * @param  array  $cc
     *
     * @return  self
     */ 
    public function setCc(array $cc)
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * Get the value of subject
     *
     * @return  string
     */ 
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the value of subject
     *
     * @param  string  $subject
     *
     * @return  self
     */ 
    public function setSubject(string $subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the value of message
     *
     * @return  string
     */ 
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @param  string  $message
     *
     * @return  self
     */ 
    public function setMessage(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of template
     *
     * @return  string
     */ 
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set the value of template
     *
     * @param  string  $template
     *
     * @return  self
     */ 
    public function setTemplate(string $template)
    {
        $this->template = $template;

        return $this;
    }
}