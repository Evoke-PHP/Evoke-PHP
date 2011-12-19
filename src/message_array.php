<?php


/// Message_Array an array of messages with titles.
class Message_Array
{
   /// The message array controlled by the class.
   private $messageArray;

   /// Construct an empty message array.
   public function __construct()
   {
      $this->messageArray = array();
   }

   /******************/
   /* Public Methods */
   /******************/

   /** Add a message to the message array.
    *  @param title \string The title of the message.
    *  @param message \string The description of the message.
    */
   public function add($title, $message)
   {
      $this->messageArray[] = array('Title' => $title, 'Message' => $message);
   }
   
   /** Append another message array onto the current message array.
    */
   public function append(Message_Array $messageArray)
   {
      $appendMessages = $messageArray->get();

      foreach ($appendMessages as $msg)
      {
	 $this->messageArray[] = $msg;
      }
   }
   
   /** Get a copy of the private message array that stores the messages.
    *  \return \array A copy of the array of messages.
    */
   public function get()
   {
      return $this->messageArray;
   }

   /// Check whether there are any messages stored in the message array.
   public function isEmpty()
   {
      return empty($this->messageArray);
   }

   /// Reset the message array.
   public function reset()
   {
      $this->messageArray = array();
   }
}

// EOF