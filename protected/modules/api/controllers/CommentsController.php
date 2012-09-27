<?php

/**
 * Work with user comment
 * @package api
 */
class CommentsController extends ApiController {

    public function actionGetComment($id) {}
    public function actionGetUserList($id, $limit, $start = null) {}
    public function actionGetEntityList($id, $entity, $limit = null, $start = null) {
        $res = array();
        $class = $entity . 'Comments';
        $criteria = new CDbCriteria;
        $criteria->condition = 'entity_id = :entity_id and published = :published';
        $criteria->params = array(':entity_id' => $id, 
                                  ':published' => CommentApi::STATUS_PUBLISHED);

        if ($limit) {
            $criteria->limit = $limit;
        }

        if ($order) {
            $criteria->offset = $order;
        }
        
        return $class::model()->with('users')->findAll($criteria);
    }
    
    public function actionPostComment($value, $user_id, $entity, $parent = 0) {}
    
    public function actionDeliteComment($id, $entity, $recursive = true) {}
    
    public function actionPutComment($id, $entity, $params = array()) {}
    
}