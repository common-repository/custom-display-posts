<?php
/*
  Plugin Name: Custom Display Posts
  Plugin URI: http://blog.950buy.com/custom-display-posts/
  Version: 1.1.2
  Author: Rueben
  License: GPL
  Author URI: http://blog.950buy.com/
  Description: This plugin allows you to easily custom display post on your site. It supports single or more category display,Support show the post comments count,Support order by Posts today, yesterday, 7 days, 1 month, 3 months, 6 months, 12 months.
 */

class CustomDisplayPosts extends WP_Widget {

    function CustomDisplayPosts() {
        parent::WP_Widget(false, $name = 'Custom Display Posts');
    }

    function widget($args, $instance) {
        global $wpdb;
        extract($args);
        $title = apply_filters('widget_title', empty($instance['title']) ? __('Custom Display Posts') : $instance['title']);
        $instance['numposts'] = empty($instance['numposts']) ? '5' : $instance['numposts'];
        echo $before_widget . $before_title . $title . $after_title;
        echo '<ul>';
        $ResultsPostID = CustomDisplayPosts_ResultsPostID($instance['post_category'], $instance['byorder'], $instance['numposts'], $instance['byDesc'], $instance['byDate']);
        if (isset($ResultsPostID)) {
            foreach ($ResultsPostID as $_results) {
                $postID = $_results->ID;
                $sql = "SELECT ID,post_title,post_excerpt,post_content,comment_count FROM " . $wpdb->posts . " where ID='$postID'";
                $row = $wpdb->get_row($sql);
                if ($instance['comment_count'] == 'y') {
                    $show_comment = " (" . $row->comment_count . ")";
                } else {
                    $show_comment = "";
                }
                if ($instance['display'] == 'excerpt') {
                    echo '<li><h2><a href="' . get_permalink($row->ID) . '" title="' . $row->post_title . '">' . $row->post_title . $show_comment . '</a></h2>';
                    echo $row->post_excerpt . '</li>';
                } elseif ($instance['display'] == 'content') {
                    echo '<li><h2><a href="' . get_permalink($row->ID) . '" title="' . $row->post_title . '">' . $row->post_title . $show_comment . '</a></h2>';
                    echo $row->post_content . '</li>';
                } else {
                    echo '<li><a href="' . get_permalink($row->ID) . '" title="' . $row->post_title . '">' . $row->post_title . $show_comment . '</a></li>';
                }
            }
        } else {
            echo '<li>No data</li>';
        }
        echo '</ul>';
        echo $after_widget;
    }

    function update($new_instance, $old_instance) {
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['post_category'] = $_POST['post_category'];
        $instance['numposts'] = $new_instance['numposts'];
        $instance['byorder'] = $new_instance['byorder'];
        $instance['display'] = $new_instance['display'];
        $instance['comment_count'] = $new_instance['comment_count'];
        $instance['byDesc'] = $new_instance['byDesc'];
        $instance['byDate'] = $new_instance['byDate'];
        return $instance;
    }

    function form($instance) {
        $categories = $instance['post_category'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> </label>
            <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
        </p>  

        <p>
            <label for="cat"><?php _e('Categories:'); ?> </label>
        <ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
            <?php wp_category_checklist(0, false, $categories, false); ?>
        </ul>
        </p> 

        <p>
            <label for="<?php echo $this->get_field_id('numposts'); ?>"><?php _e('Number:'); ?> </label>
            <input type="text" id="<?php echo $this->get_field_id('numposts'); ?>" name="<?php echo $this->get_field_name('numposts'); ?>" value="<?php echo $instance['numposts']; ?>" />
        </p>  

        <p>
            <label for="<?php echo $this->get_field_id('display'); ?>"><?php _e('Display:'); ?></label> 
            <select id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>">
                <option value="title"<?php if ($instance['display'] == 'title') { ?> selected="selected" <?php } ?>>Title Only</option>
                <option value="excerpt"<?php if ($instance['display'] == 'excerpt') { ?> selected="selected" <?php } ?>>Title and Excerpt</option>
                <option value="content"<?php if ($instance['display'] == 'content') { ?> selected="selected" <?php } ?>>Title and Content</option>
            </select>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('byorder'); ?>"><?php _e('Order By:'); ?></label> </p>
        <select id="<?php echo $this->get_field_id('byorder'); ?>" name="<?php echo $this->get_field_name('byorder'); ?>">
            <option value="post_id"<?php if ($instance['byorder'] == 'post_id') { ?> selected="selected" <?php } ?>>Post ID</option>
            <option value="comments"<?php if ($instance['byorder'] == 'comments') { ?> selected="selected" <?php } ?>>Comment count</option>
        </select><br>        
        <select id="<?php echo $this->get_field_id('byDesc'); ?>" name="<?php echo $this->get_field_name('byDesc'); ?>">
            <option value="desc"<?php if ($instance['byDesc'] == 'desc') { ?> selected="selected" <?php } ?>>Desc</option>
            <option value="asc"<?php if ($instance['byDesc'] == 'asc') { ?> selected="selected" <?php } ?>>Asc</option>
            <option value="random"<?php if ($instance['byDesc'] == 'random') { ?> selected="selected" <?php } ?>>Random</option>
        </select><br>
        <select id="<?php echo $this->get_field_id('byDate'); ?>" name="<?php echo $this->get_field_name('byDate'); ?>">
            <option value="all"<?php if ($instance['byDate'] == 'all') { ?> selected="selected" <?php } ?>>All</option>
            <option value="today"<?php if ($instance['byDate'] == 'today') { ?> selected="selected" <?php } ?>>Today</option>
            <option value="yesterday"<?php if ($instance['byDate'] == 'yesterday') { ?> selected="selected" <?php } ?>>Yesterday</option>
            <option value="week"<?php if ($instance['byDate'] == 'week') { ?> selected="selected" <?php } ?>>7 days</option>
            <option value="1"<?php if ($instance['byDate'] == '1') { ?> selected="selected" <?php } ?>>1 month</option>
            <option value="3"<?php if ($instance['byDate'] == '3') { ?> selected="selected" <?php } ?>>3 months</option>
            <option value="6"<?php if ($instance['byDate'] == '6') { ?> selected="selected" <?php } ?>>6 months</option>
            <option value="12"<?php if ($instance['byDate'] == '12') { ?> selected="selected" <?php } ?>>12 months</option>
        </select><br><br>
        <p>
            <label for="<?php echo $this->get_field_id('comment_count'); ?>"><?php _e('Show Comment count:'); ?> </label>
            <input type="checkbox" id="<?php echo $this->get_field_id('comment_count'); ?>" name="<?php echo $this->get_field_name('comment_count'); ?>" value="y" <?php if ($instance['comment_count'] == 'y') {
                echo "checked";
            } ?> />yes
        </p> 


        <?php
    }

}

function custom_display_posts_init() {
    register_widget('CustomDisplayPosts');
}

add_action('widgets_init', 'custom_display_posts_init');

function CustomDisplayPosts_CatToStr($categories) {
    $cat = "";
    if (!empty($categories)) {
        if (is_array($categories)) {
            $cat = implode(",", $categories);
        } else {
            $cat = $categories;
        }
    }
    return $cat;
}

function CustomDisplayPosts_ResultsPostID($categories, $order, $numposts = 10, $byDesc, $byDate) {
    global $wpdb;
    $cat = CustomDisplayPosts_CatToStr($categories);
    $sql = "SELECT DISTINCT  " . $wpdb->posts . ".ID," . $wpdb->posts . ".comment_count as c_count FROM  " . $wpdb->posts . "
  INNER JOIN " . $wpdb->term_relationships . " ON (" . $wpdb->posts . ".ID = " . $wpdb->term_relationships . ".object_id)
  INNER JOIN " . $wpdb->terms . " ON (" . $wpdb->term_relationships . ".term_taxonomy_id = " . $wpdb->terms . ".term_id)
  INNER JOIN " . $wpdb->term_taxonomy . " ON (" . $wpdb->terms . ".term_id = " . $wpdb->term_taxonomy . ".term_id) WHERE
   " . $wpdb->posts . ".post_status = 'publish'  and  " . $wpdb->term_taxonomy . ".taxonomy='category'  and  " . $wpdb->posts . ".post_type = 'post'";

    if (!empty($cat)) {
        $sql.=" and wp_terms.term_id in ($cat)";
    }
    switch ($byDate) {
        case "today":
            $sql.=" and DATE_FORMAT(now(), '%Y-%m%-%d') = DATE_FORMAT(" . $wpdb->posts . ".post_date, '%Y-%m%-%d')";
            break;
        case "yesterday":
            $sql.=" and DATE_ADD(DATE_FORMAT(now(), '%Y-%m%-%d'),INTERVAL -1 DAY) = DATE_FORMAT(" . $wpdb->posts . ".post_date, '%Y-%m%-%d')";
            break;
        case "week":
            $sql.=" and DATE_ADD(DATE_FORMAT(now(), '%Y-%m%-%d'),INTERVAL -7 DAY) <= DATE_FORMAT(" . $wpdb->posts . ".post_date, '%Y-%m%-%d')";
            break;
        case "1":
            $sql.=" and DATE_ADD(DATE_FORMAT(now(), '%Y-%m%-%d'),INTERVAL -1 MONTH) <= DATE_FORMAT(" . $wpdb->posts . ".post_date, '%Y-%m%-%d')";
            break;
        case "3":
            $sql.=" and DATE_ADD(DATE_FORMAT(now(), '%Y-%m%-%d'),INTERVAL -3 MONTH) <= DATE_FORMAT(" . $wpdb->posts . ".post_date, '%Y-%m%-%d')";
            break;
        case "6":
            $sql.=" and DATE_ADD(DATE_FORMAT(now(), '%Y-%m%-%d'),INTERVAL -6 MONTH) <= DATE_FORMAT(" . $wpdb->posts . ".post_date, '%Y-%m%-%d')";
            break;
        case "12":
            $sql.=" and DATE_ADD(DATE_FORMAT(now(), '%Y-%m%-%d'),INTERVAL -12 MONTH) <= DATE_FORMAT(" . $wpdb->posts . ".post_date, '%Y-%m%-%d')";
            break;
        default:
            $sql.="";
            break;
    }
    if ($order == 'comments') {
        if ($byDesc == 'asc') {
            $sql.=" order by c_count asc";
        } elseif ($byDesc == 'random') {
            $sql.=" order by rand()";
        } else {
            $sql.=" order by c_count desc";
        }
    } else {
        if ($byDesc == 'asc') {
            $sql.=" order by " . $wpdb->posts . ".ID asc";
        } elseif ($byDesc == 'random') {
            $sql.=" order by rand()";
        } else {
            $sql.=" order by " . $wpdb->posts . ".ID desc";
        }
    }

    if (!isset($numposts)) {
        $sql.=" limit 5";
    } else {
        $sql.=" limit $numposts";
    }
    return $wpdb->get_results($sql);
}
?>