<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Photo_model extends Default_Model {

    public function __construct() {
        parent::__construct();

        $this->_table = 'photo';
        $this->_pk = 'id';
    }

    /**
     * Get a list of albums (all or specified owner)
     *
     * @param int $owner_id (optional) - ID album owner (user_id)
     * @return array
     */
    public function getAlbumsList($owner_id = '') {
        if (!empty($owner_id)) {
            $this->db->where('owner', $owner_id);
        }
        $res = $this->db->get('photo_album');
        return $res !== FALSE ? $res->result_array() : array();
    }

    /**
     * Get photos related with the specified album
     *
     * @param int $id - album ID
     * @param int $user_id - user ID
     * @param array $privacy (optional)
     * @return array
     */
    public function getAlbumPhotos($id, $user_id, $privacy = array()) {
        $photos = array();
        $this->db
                ->select('p.*, pa.owner, if(ps.shared is not null, 1, 0) as shared', false)
                ->from('photo as p')
                ->join('photo_album as pa', 'p.album_id = pa.id')
                ->join('photo_share as ps', 'ps.photo_id = p.id AND ps.user_id = ' . $user_id, 'left')
                ->where('p.album_id', $id)
                ->order_by('p.id');

        if(count($privacy) > 0) {
            $privacy = array_filter($privacy);
            $this->db->where('('.($privacy ? 'privacy_code IN ("'. implode('","', $privacy) . '") OR ' : '').'privacy_code = "" OR privacy_code is null)', null,false);
        }

        $result = $this->db->get();

        if ($result !== FALSE) {
            foreach ($result->result_array() as $row) {
                $photos[] = array(
                    'id' => $row['id'],
                    'orig' => sprintf(ORIG_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']),
                    'medium' => sprintf(MEDIUM_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']),
                    'thumb' => sprintf(THUMB_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']),
                    'privacy_code' => $row['privacy_code'],
                    'shared' => $row['shared']
                );
            }
        }
        return $photos;
    }

    /**
     * Get an array of all albums with photos
     *
     * @param int $owner_id (optional) - ID album owner (user_id)
     * @return array
     */
    public function getAlbumsWithPhotos($owner_id = '') {
        $albums = array();
        $this->db
                ->select('a.id as album_id, a.title as album_title, p.id, p.image_filename, a.owner')
                ->from('photo_album as a')
                ->join('photo as p', 'p.album_id = a.id', 'left');
        if (!empty($owner_id)) {
            $this->db->where('a.owner', $owner_id);
        }
        $res = $this->db->order_by('p.id')->get();
        if ($res !== FALSE) {
            foreach ($res->result_array() as $row) {
                if (!isset($albums[$row['album_id']])) {
                    $albums[$row['album_id']] = array(
                        'album_title' => '',
                        'photos' => array()
                    );
                }
                $albums[$row['album_id']]['album_title'] = $row['album_title'];
                if ($row['id']) {
                    $albums[$row['album_id']]['photos'][] = array(
                        'id' => $row['id'],
                        'orig' => sprintf(ORIG_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']),
                        'medium' => sprintf(MEDIUM_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']),
                        'thumb' => sprintf(THUMB_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename'])
                    );
                }
            }
        }
        return $albums;
    }

    /**
     * Get album data
     *
     * @param int $album_id
     * @return boolean or object
     */
    public function getAlbumByID($album_id) {
        $res = $this->db->from('photo_album')->where('id', $album_id)->limit(1)->get();
        if ($res !== FALSE && $res->num_rows() === 1) {
            return $res->row();
        }
        return FALSE;
    }

    /**
     * Save uploaded photos to an album in the database
     *
     * @param int $album_id - album ID
     * @param int $dir - path to the directory with uploaded photos
     * @return boolean
     */
    public function saveAlbumPhoto($album_id, $dir) {
        $rows = array();
        foreach (glob($dir . '/*.{jpg,png,gif,jpeg}', GLOB_BRACE) as $filename) {
            $rows[] = array(
                'album_id' => $album_id,
                'title' => '',
                'image_filename' => basename($filename),
                'privacy_code' => NULL
            );
        }
        return $this->insertRow($rows, '', true);
    }

    /**
     * Delete specified album
     *
     * @param int $id - album ID
     * @return boolean
     */
    public function deleteAlbum($id) {
        $this->db->trans_start();

        //delete comment share
        $query = "DELETE pcs.* FROM photo_comment_share AS pcs JOIN photo_comment AS pc ON (pc.id = pcs.comment_id) JOIN photo AS p ON (p.id = pc.target_id) WHERE p.album_id = {$id}";
        $this->db->query($query);
        //delete comment votes
        $query = "DELETE pcv.* FROM photo_comment_vote AS pcv JOIN photo_comment AS pc ON (pc.id = pcv.comment_id) JOIN photo AS p ON (p.id = pc.target_id) WHERE p.album_id = {$id}";
        $this->db->query($query);
        //delete photo votes
        $query = "DELETE pv.* FROM photo_vote AS pv JOIN photo AS p ON (p.id = pv.photo_id) WHERE p.album_id = {$id}";
        $this->db->query($query);
        //delete photo comments
        $query = "DELETE pc.* FROM photo_comment AS pc JOIN photo AS p ON (p.id = pc.target_id) WHERE p.album_id = {$id}";
        $this->db->query($query);
        //delete photo
        $query = "DELETE FROM photo WHERE album_id = {$id}";
        $this->db->query($query);
        //delete album
        $query = "DELETE FROM photo_album WHERE id = {$id}";
        $this->db->query($query);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Delete specified photo
     *
     * @param int $id - photo ID
     * @return boolean
     */
    public function deletePhoto($id) {
        $this->db->trans_start();

        //delete comment share
        $query = "DELETE pcs.* FROM photo_comment_share AS pcs JOIN photo_comment AS pc ON (pc.id = pcs.comment_id) WHERE pc.target_id = {$id}";
        $this->db->query($query);
        //delete comment votes
        $query = "DELETE pcv.* FROM photo_comment_vote AS pcv JOIN photo_comment AS pc ON (pc.id = pcv.comment_id) WHERE pc.target_id = {$id}";
        $this->db->query($query);
        //delete photo votes
        $query = "DELETE FROM photo_vote WHERE photo_id = {$id}";
        $this->db->query($query);
        //delete photo comments
        $query = "DELETE FROM photo_comment WHERE target_id = {$id}";
        $this->db->query($query);
        //delete photo
        $query = "DELETE FROM photo WHERE id = {$id}";
        $this->db->query($query);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    /**
     * Update photo privacy code
     *
     * @param int $photo_id
     * @param string $privacy_code
     * @return boolean
     */
    public function updatePrivacyCode($photo_id, $privacy_code = null) {
        $this->db->trans_start();
        $upd_query = "UPDATE photo SET privacy_code = " . (is_null($privacy_code) ? 'NULL' : "'{$privacy_code}'") . " WHERE id = {$photo_id}";
        $this->db->query($upd_query);
        $del_query = "DELETE FROM photo_share WHERE photo_id = {$photo_id}";
        $this->db->query($del_query);
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Get photo by specified ID
     *
     * @param int $id - photo ID
     * @param int $user_id (optional)
     * @return null or object
     */
    public function getPhotoById($id, $user_id = null) {
        $this->db
                ->select('a.id as album_id, a.owner, p.*')
                ->from('photo as p')
                ->join('photo_album as a', 'p.album_id = a.id')
                ->where('p.id', $id);
        if(!is_null($user_id)) {
            $this->db
                    ->select('v.type as my_vote, if(ps.shared is not null, 1, 0) as shared', false)
                    ->join('photo_vote as v', 'v.photo_id = p.id AND v.user_id = ' . $user_id, 'left')
                    ->join('photo_share as ps', 'ps.photo_id = p.id AND ps.user_id = ' . $user_id, 'left');
        }

        $photo = $this->db->limit(1)->get($this->_table);

        if ($photo !== FALSE && $photo->num_rows() == 1) {

            $photo = $photo->row();
            $photo->orig = sprintf(ORIG_PHOTO_PATH, $photo->owner, $photo->album_id, $photo->image_filename);
            $photo->medium = sprintf(MEDIUM_PHOTO_PATH, $photo->owner, $photo->album_id, $photo->image_filename);
            $photo->thumb = sprintf(THUMB_PHOTO_PATH, $photo->owner, $photo->album_id, $photo->image_filename);

            return $photo;
        }

        return NULL;
    }

    /**
     * Set privacy settings for access to the photos for a particular friend
     *
     * @param int $user_id
     * @param int $friend_id
     * @param string $privacy
     * @return boolean
     */
    public function setFriendPrivacy($user_id, $friend_id, $privacy) {
        $this->db
                ->where('from_user', $user_id)
                ->where('to_user', $friend_id)
                ->where('type', 'photo');
        $res = $this->db->get('privacy_permission');
        if ($res !== FALSE) {
            if ($res->num_rows() > 0) {
                $this->db
                        ->where('from_user', $user_id)
                        ->where('to_user', $friend_id)
                        ->where('type', 'photo');
                if(empty($privacy)) {
                    $res = $this->db->delete('privacy_permission');
                } else {
                    $res = $this->db->set('privacy', $privacy)->update('privacy_permission');
                }
            } else {
                $row = array(
                    'from_user' => $user_id,
                    'to_user'   => $friend_id,
                    'type'      => 'photo',
                    'privacy'   => $privacy
                );
                $res = $this->insertRow($row, 'privacy_permission');
            }
        }
        return $res !== FALSE;
    }

    /**
     * Share photo
     *
     * @param int $user_id - user ID
     * @param int $photo_id - photo ID
     * @return boolean
     */
    public function sharePhoto($user_id, $photo_id) {
        $shared = date("Y-m-d H:i:s");
        $row = array(
            'user_id' => $user_id,
            'photo_id' => $photo_id,
            'shared' => $shared
        );
        $res = $this->insertRow($row, 'photo_share');
        return $res;
    }

    /**
     * Unshare photo
     *
     * @param int $user_id - user ID
     * @param int $photo_id - photo ID
     * @return boolean
     */
    public function unsharePhoto($user_id, $photo_id) {
        $this->db
                ->where('user_id', $user_id)
                ->where('photo_id', $photo_id);
        $res = $this->db->delete('photo_share');
        return $res;
    }

    /**
     * Get user's shared photos
     *
     * @param int $user_id - user ID
     * @return array
     */
    public function getUserSharedPhotos($user_id) {
        $photos = array();
        $res = $this->db
                ->select('p.id, a.owner, p.album_id, p.image_filename')
                ->from('photo as p')
                ->join('photo_album as a', 'p.album_id = a.id')
                ->join('photo_share as ps', 'ps.photo_id = p.id AND ps.user_id = ' . $user_id)
                ->get();
        if($res !== FALSE) {
            foreach($res->result_array() as $row) {
                $photos[$row['id']] = array(
                    'original' => sprintf(ORIG_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']),
                    'medium' => sprintf(MEDIUM_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']),
                    'thumb' => sprintf(THUMB_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename'])
                );
            }
        }
        return $photos;
    }

    /**
     * Get shared photos (for homepage updates)
     *
     * @param int $user_id - user ID
     * @param int $last_id
     */
    public function getSharedPhotos($user_id, $last_id) {
        $this->load->model('friend_model', 'friends');
        $ids = $this->friends->getFriendsIDs($user_id);
        $ids[] = $user_id;

        $subquery = "SELECT v.photo_id, substring_index(group_concat(u.username SEPARATOR '|'), '|', 10) as who_voted FROM photo_vote as v JOIN user_profile AS u USING (user_id) WHERE v.type = '%s' GROUP BY photo_id";
        $select_fields = array(
            '"photo" as target',
            'p.id',
            'p.title as photo_title',
            'p.image_filename',
            'p.album_id',
            'a.owner',
            'a.title as album_title',
            'shp.id as shared_id',
            'shp.user_id',
            'shp.shared',
            'u.username',
            'u.profile_image',
            't.like_cnt',
            't.dislike_cnt',
            't2.who_voted as who_voted_like',
            't3.who_voted as who_voted_dislike',
            's.shares_count',
            'v.type as my_vote',
            'ps.photo_id as my_share'
        );
        $from = 'photo as p';
        $join = "JOIN photo_album as a ON (p.album_id = a.id)";
        $join .= " JOIN photo_share as shp ON (shp.photo_id = p.id)";
        $join .= " LEFT JOIN user_profile as u ON (u.user_id = shp.user_id)";
        $join .= " LEFT JOIN (SELECT photo_id, SUM(IF(type='like', 1, 0)) AS like_cnt, SUM(IF(type='dislike', 1, 0)) AS dislike_cnt FROM photo_vote GROUP BY photo_id) as t ON (t.photo_id = p.id)";
        $join .= " LEFT JOIN (" . sprintf($subquery, "like") . ") as t2 ON (t2.photo_id = p.id)";
        $join .= " LEFT JOIN (" . sprintf($subquery, "dislike") . ") as t3 ON (t3.photo_id = p.id)";
        $join .= " LEFT JOIN (SELECT photo_id, COUNT(photo_id) AS shares_count FROM photo_share GROUP BY photo_id) as s ON (p.id = s.photo_id)";
        $join .= " LEFT JOIN photo_vote as v ON (v.photo_id = p.id AND v.user_id = {$user_id})";
        $join .= " LEFT JOIN photo_share as ps ON (ps.photo_id = p.id AND ps.user_id = {$user_id})";
        $where = "shp.user_id IN (" . implode(',', $ids) . ") AND (p.privacy_code is null OR p.privacy_code = '')";
        if($last_id > 0) {
            $where .= " AND shp.id < {$last_id}";
        }
        
        $comments = array();
        
        $query = "SELECT " . implode(',', $select_fields) . " FROM {$from} {$join} WHERE {$where} ORDER BY shp.id DESC LIMIT " . UPDATES_LIMIT;
        $res = $this->db->query($query);

        if($res !== FALSE) {
            $this->load->model('auth_model', 'authm');
            foreach($res->result_array() as $row) {
                $key = $this->server_to_client_localtime($row['shared']);
                $row['date'] = date('\o\n l, F j, Y \a\t g.ia', $key);
                $row['unix_date'] = $key;
                $tmp_inf = array(
                    'user_id' => $row['user_id'],
                    'profile_image' => $row['profile_image']
                );
                $row['ava'] = $this->authm->get_user_photo('forum', $tmp_inf);
                $row['original'] = sprintf(ORIG_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']);
                $row['medium'] = sprintf(MEDIUM_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']);
                $comments[$row['shared_id']] = $row;
            }
            $res->free_result();
        }
        
        return $comments;
    }

    /**
     * Get user's public photos
     *
     * @param int $user_id - user ID
     * @return array
     */
    public function getUserPublicPhotos($user_id) {
        $photos = array();
        $res = $this->db
                ->select('p.id, a.owner, p.album_id, p.image_filename')
                ->from('photo as p')
                ->join('photo_album as a', 'p.album_id = a.id')
                ->where('a.owner', $user_id)
                ->where('(p.privacy_code = "" OR p.privacy_code IS NULL)', null, false)
                ->get();
        if($res !== FALSE) {
            foreach($res->result_array() as $row) {
                $photos[$row['id']] = array(
                    'original' => sprintf(ORIG_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']),
                    'medium' => sprintf(MEDIUM_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename']),
                    'thumb' => sprintf(THUMB_PHOTO_PATH, $row['owner'], $row['album_id'], $row['image_filename'])
                );
            }
        }
        return $photos;
    }
    
    /**
     * Get the owner ID on this photo
     * 
     * @param int $photo_id - photo ID
     * @return mixed
     */
    public function getPhotoOwner($photo_id) {
        $this->db
                ->select('alb.owner')
                ->from('photo_album as alb')
                ->join($this->_table . ' as p', "p.album_id = alb.id AND p.id = {$photo_id}")
                ->limit(1);
        $owner = $this->db->get();
        return $owner !== FALSE && $owner->num_rows() == 1 ? $owner->row()->owner : null;
    }
    
    /**
     * Check if this photo from my album
     * 
     * @param int $user_id - user ID
     * @param int $photo_id - photo ID
     * @return boolean
     */
    public function isMyPhoto($user_id, $photo_id) {
        $owner = $this->getPhotoOwner($photo_id);
        return $owner && $owner == $user_id;
    }

}

/* End of file photo_model.php */
/* Location: ./application/models/photo_model.php */