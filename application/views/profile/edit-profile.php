<?php
$data = array(
    "top_menu" => "edit_profile",
    "additional_css" => array("datepicker.css")
);

$this->load->view('include/head', $data);

$user_info = $this->session->userdata['user_info'];
$language = array(
    'english' => 'en',
    //'dutch' => 'nl'
);
?>
<div class="edit-profile">
    <form name="frm-edit-pro" id="frm-edit-pro">
        <div class="frm-edit-info">
            <input type="hidden" value="<?= $user_info['id'] ?>" name="profile_id" id="profile_id"/>
            <ul>
                <li>
                    <label>Username</label>
                    <input type="text" name="username" id="username" value="<?= $user_info['username'] ?>" maxlength="30" style="width: 375px;"/>
                </li>
                <li>
                    <label>Living in</label>
                    <input type="text" name="living" id="living" value="<?= implode(', ', array_filter(array($user_info['country'], $user_info['state'], $user_info['city']))) ?>" maxlength="305" style="width: 375px;"/>
                    <input type="hidden" id="country" name="living_country" value="<?= $user_info['country'] ?>" maxlength="100"/>
                    <input type="hidden" id="administrative_area_level_1" name="living_state" value="<?= $user_info['state'] ?>" maxlength="100"/>
                    <input type="hidden" id="locality" name="living_city" value="<?= $user_info['city'] ?>" maxlength="100"/>
                    <a onclick="clearAddress(); return false;"><span class="glyphicon glyphicon-trash"></span></a>
                </li>
                <li>
                    <label>Birthday</label>
                    <input type="text" name="age" id="age" value="<?= !empty($user_info['birthday']) ? date("m/d/Y", strtotime($user_info['birthday'])) : '' ?>"/>
                </li>
                <li>
                    <label>Education</label>
                    <select name="education" id="education">
                        <?php
                        foreach ($this->base->educationArr as $key => $val) {
                            echo '<option value="' . $key . '" ' . ($key == $user_info['education'] ? "selected" : "") . '>' . $val . '</option>';
                        }
                        ?>
                    </select>
                </li>
                <li>
                    <label>Gender</label>
                    <select id="gender" name="gender">
                        <?php
                        foreach ($this->base->genderArr as $key => $val) {
                            echo '<option value="' . $key . '" ' . ($key == $user_info['gender'] ? "selected" : "") . '>' . $val . '</option>';
                        }
                        ?>
                    </select>
                </li>
                <li>
                    <label>Sexual orientation</label>
                    <select name="sexual_ori" id="sexual_ori">
                        <?php
                        foreach ($this->base->orientationArr as $key => $val) {
                            echo '<option value="' . $key . '" ' . ($key == $user_info['orientation'] ? "selected" : "") . '>' . $val . '</option>';
                        }
                        ?>
                    </select>
                </li>
                <li>
                    <label>Relationship Status</label>
                    <select name="rel_status" id="rel_status">
                        <?php
                        foreach ($this->base->relationshipArr as $key => $val) {
                            echo '<option value="' . $key . '" ' . ($key == $user_info['relationship'] ? "selected" : "") . '>' . $val . '</option>';
                        }
                        ?>
                    </select>
                </li>
            </ul>
        </div>
        <div class="modal-footer ifrm-footer"><button type="button" class="btn btn-green" onclick="$('#frm-edit-pro').submit();">Update</button></div>
    </form>
</div>
<script type="text/javascript" src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/jquery.cookie.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/all_site/ajax.js') ?>"></script>
<!--script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&language=<?=$language[$this->session->userdata('language')]?>"></script-->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&language=en"></script>
<script type="text/javascript" src="<?= base_url('assets/js/bootstrap-datepicker.js') ?>"></script>
<script type="text/javascript" src="<?= base_url('assets/js/profile/profile-edit.js') ?>"></script>
</body>
</html>