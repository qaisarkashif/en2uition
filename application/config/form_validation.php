<?php
/* Form validations rules*/

$config = array(
   array(
		 'field'   => 'email',
		 'label'   => 'Email',
		 'rules'   => 'required'
	  ),
   array(
		 'field'   => 'password',
		 'label'   => 'Password',
		 'rules'   => 'required'
	  ),
   array(
		 'field'   => 'passconf',
		 'label'   => 'Password Confirmation',
		 'rules'   => 'required'
	  )
);

?>