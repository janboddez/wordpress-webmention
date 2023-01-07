<?php
class DB_Test extends WP_UnitTestCase {
	public function test_update_database() {
		require_once( dirname( __FILE__ ) . '/../includes/class-db.php' );

		$comment_id = wp_insert_comment(
			array(
				'comment_author' => 'Test',
				'comment_author_email' => 'test@example.org',
				'comment_content' => 'test comment',
				'comment_type' => 'webmention',
				'comment_meta' => array(
					'semantic_linkbacks_source' => 'https://example.org/source',
					'semantic_linkbacks_avatar' => 'https://example.org/avatar',
					'semantic_linkbacks_canonical' => 'https://example.org/canonical',
					'semantic_linkbacks_author_url' => 'https://example.org/author_url',
					'semantic_linkbacks_type' => 'reply'
				),
			)
		);

		\Webmention\DB::update_database();

		wp_cache_flush();

		$metas = get_comment_meta( $comment_id );

		$this->assertEquals( $metas['avatar'][0], 'https://example.org/avatar' );
		$this->assertEquals( $metas['protocol'][0], 'webmention' );
		$this->assertEquals( $metas['webmention_source'][0], 'https://example.org/source' );
		$this->assertEquals( $metas['webmention_canonical'][0], 'https://example.org/canonical' );
		$this->assertEquals( $metas['webmention_author_url'][0], 'https://example.org/author_url' );

		$comment = get_comment( $comment_id );

		$this->assertEquals( $comment->comment_type, 'reply' );
	}
}