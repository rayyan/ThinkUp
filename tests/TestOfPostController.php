<?php
require_once dirname(__FILE__).'/init.tests.php';
require_once THINKUP_ROOT_PATH.'webapp/_lib/extlib/simpletest/autorun.php';
require_once THINKUP_ROOT_PATH.'webapp/config.inc.php';

/**
 * Test of Post Controller
 *
 * @author Gina Trapani <ginatrapani[at]gmail[dot]com>
 *
 */
class TestOfPostController extends ThinkUpUnitTestCase {

    /**
     * Constructor
     */
    public function __construct() {
        $this->UnitTestCase('PostController class test');
    }

    /**
     * Add test post to database
     */
    public function setUp(){
        parent::setUp();
        $q = "INSERT INTO tu_posts (post_id, author_user_id, author_username, author_fullname, author_avatar,
        post_text, source, pub_date, reply_count_cache, retweet_count_cache) VALUES (1001, 13, 'ev', 'Ev Williams', 
        'avatar.jpg', 'This is a test post', 'web', '2006-01-01 00:05:00', ".rand(0, 4).", 5);";
        $this->db->exec($q);
    }

    /**
     * Test constructor
     */
    public function testConstructor() {
        $controller = new PostController(true);
        $this->assertTrue(isset($controller), 'constructor test');
    }

    /**
     * Test controller when user is not logged in
     */
    public function testControlNotLoggedIn() {
        $controller = new PostController(true);
        $results = $controller->go();

        $this->assertTrue(strpos($results, 'Public Timeline') > 0);
    }

    /**
     * Test controller when user is logged in, but there's no Post ID on the query string
     */
    public function testControlLoggedInNoPostID() {
        $this->simulateLogin('me@example.com');

        $controller = new PostController(true);
        $results = $controller->go();

        $this->assertTrue(strpos( $results, "Post not found") > 0, "no post");
    }

    /**
     * Test controller when user is logged in and there is a valid Post ID on the query string
     */
    public function testControlLoggedInWithPostID() {
        $this->simulateLogin('me@example.com');
        $_GET["t"] = '1001';

        $controller = new PostController(true);
        $results = $controller->go();

        $this->assertTrue(strpos( $results, "This is a test post") > 0, "no post");
    }

    /**
     * Test controller when logged in but there's a numeric but nonexistent Post ID
     */
    public function testControlLoggedInWithNumericButNonExistentPostID(){
        $this->simulateLogin('me@example.com');
        $_GET["t"] = '11';

        $controller = new PostController(true);
        $results = $controller->go();

        $this->assertTrue(strpos( $results, "Post not found") > 0, "no post");
    }

    /**
     * Test controller when logged in but a non-numeric post ID
     */
    public function testControlLoggedInWithNonNumericPostID(){
        $this->simulateLogin('me@example.com');
        $_GET["t"] = 'notapostID45';

        $controller = new PostController(true);
        $results = $controller->go();

        $this->assertTrue(strpos( $results, "Post not found") > 0, "no post");
    }
}