<?php
class News_Entity_Likes extends EMongoEmbeddedDocument
{
    
    public $count;
    public $users; // array(id => login, id => login)
    
    // We can define rules for fields, just like in normal CModel/CActiveRecord classes
    public function rules()
    {
        return array(
            array('count, users', 'required'),
        );
    }
}