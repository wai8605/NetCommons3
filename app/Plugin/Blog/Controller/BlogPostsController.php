<?php
/**
 * BlogPostsControllerクラス
 *
 * <pre>
 * ブログ記事投稿画面用コントローラ
 * </pre>
 *
 * @copyright     Copyright 2012, NetCommons Project
 * @package       App.Controller
 * @author        Noriko Arai,Ryuji Masukawa
 * @since         v 3.0.0.0
 * @license       http://www.netcommons.org/license.txt  NetCommons License
 */
class BlogPostsController extends BlogAppController {

/**
 * Component name
 *
 * @var array
 */
	public $components = array('RevisionList', 'CheckAuth' => array('allowAuth' => NC_AUTH_GENERAL));

/**
 * Model name
 *
 * @var array
 */
	public $uses = array('Blog.BlogTermLink', 'Revision');

/**
 * ブログ記事投稿表示・登録
 * @param   integer $postId
 * @return  void
 * @since   v 3.0.0.0
 */
	public function index($postId = null) {
		// TODO:権限チェックが未作成
		// TODO:承認機能未実装
		// TODO:email送信未実装

		// 自動保存前処理
		$autoRegistParams = $this->RevisionList->beforeAutoRegist($postId);
		$postId = $autoRegistParams['id'];
		$isAutoRegist = $autoRegistParams['isAutoRegist'];
		$status = $autoRegistParams['status'];
		$revisionName = $autoRegistParams['revision_name'];

		$blog = $this->Blog->find('first', array('conditions' => array('content_id' => $this->content_id)));
		if(!isset($blog['Blog'])) {
			$this->flash(__('Content not found.'), null, 'BlogPosts.index.001', '404');
			return;
		}

		if(isset($postId)) {
			// 編集
			$blogPost = $this->BlogPost->findById($postId);
			if(!isset($blogPost['BlogPost'])) {
				$this->flash(__('Content not found.'), null, 'BlogPosts.index.002', '404');
				return;
			}
			if($blogPost['BlogPost']['is_future'] == _ON || $blogPost['BlogPost']['status'] == NC_STATUS_TEMPORARY) {
				$isBeforeUpdateTermCount = false;
			} else {
				$isBeforeUpdateTermCount = true;
			}

			// 自動保存で最新のデータがあった場合、表示
			$revision = $this->Revision->findRevisions(null, $blogPost['Revision']['group_id'], 1);
			if(isset($revision[0])) {
				$blogPost['Revision'] = $revision[0]['Revision'];
			} else {
				$blogPost['Revision'] = array('content' => '');
			}
		} else {
			$blog_term_links = array();
			$blogPost = $this->BlogPost->findDefault($this->content_id);
			$isBeforeUpdateTermCount = false;
		}
		$active_category_arr = null;
		$active_tag_arr = null;
		if($this->request->is('post')) {
			// 登録処理
			if(!isset($this->request->data['BlogPost']) || !isset($this->request->data['Revision']['content'])) {
				$this->flash(__('Unauthorized request.<br />Please reload the page.'), null, 'BlogPosts.index.003', '500');
				return;
			}
			unset($this->request->data['BlogPost']['id']);
			unset($this->request->data['BlogPost']['revision_group_id']);
			unset($this->request->data['BlogPost']['is_approved']);
			$blogPost['BlogPost'] = array_merge($blogPost['BlogPost'], $this->request->data['BlogPost']);
			$blogPost['BlogPost']['content_id'] = $this->content_id;
			$blogPost['BlogPost']['permalink'] = $blogPost['BlogPost']['title'];	// TODO:仮でtitleをセット「「/,:」等の記号を取り除いたり同じタイトルがあればリネームしたりすること。」
			$blogPost['BlogPost']['status'] = isset($status) ? $status : $blogPost['BlogPost']['status'];

			$blogPost['Revision']['content'] = $this->request->data['Revision']['content'];

			$fieldList = array(
				'content_id', 'post_date', 'title', 'permalink', 'icon_name', 'revision_group_id', 'status', 'is_approved',
				'post_password', 'trackback_link', 'pre_change_flag', 'pre_change_date',
			);

			$pointer = _OFF;
			if(empty($blogPost['BlogPost']['pre_change_flag']) && ($blogPost['BlogPost']['revision_group_id'] == 0 || !$isAutoRegist)) {
				$pointer = _ON;
			}

			$revision = array(
				'Revision' => array(
					'group_id' => $blogPost['BlogPost']['revision_group_id'],
					'pointer' => $pointer,
					'revision_name' => $revisionName,
					'content_id' => $this->content_id,
					'content' => $this->request->data['Revision']['content'],
				)
			);

			$fieldListRevision = array(
				'group_id', 'pointer', 'revision_name', 'content_id', 'content',
			);

			$active_category_arr = (isset($this->request->data['BlogTermLink']) && isset($this->request->data['BlogTermLink']['category_id'])) ?
				$this->request->data['BlogTermLink']['category_id'] : array();
			$active_tag_arr = (isset($this->request->data['BlogTermLink']) && isset($this->request->data['BlogTermLink']['tag_name'])) ?
				$this->request->data['BlogTermLink']['tag_name'] : array();

			$this->Revision->set($revision);
			$this->BlogPost->set($blogPost);
			if($this->BlogPost->validates(array('fieldList' => $fieldList)) && $this->Revision->validates(array('fieldList' => $fieldListRevision))) {
				$this->Revision->save($revision, false, $fieldListRevision);
				if(empty($blogPost['BlogPost']['revision_group_id'])) {
					$blogPost['BlogPost']['revision_group_id'] = $this->Revision->id;
				}
				if(strtotime($this->BlogPost->date($blogPost['BlogPost']['post_date'])) > strtotime($this->BlogPost->nowDate())) {
					// 未来の記事
					$blogPost['BlogPost']['is_future'] = _ON;
				} else {
					$blogPost['BlogPost']['is_future'] = _OFF;
				}

				$this->BlogPost->save($blogPost, false, $fieldList);

				if($isAutoRegist) {
					// 自動保存時後処理
					$this->RevisionList->afterAutoRegist($this->BlogPost->id);
					return;
				}

				if(empty($blogPost['BlogPost']['id'])) {
					$this->Session->setFlash(__('Has been successfully registered.'));
				} else {
					$this->Session->setFlash(__('Has been successfully updated.'));
				}
				if($blogPost['BlogPost']['is_future'] == _ON || $blogPost['BlogPost']['status'] == NC_STATUS_TEMPORARY) {
					$is_after_update_term_count = false;
				} else {
					$is_after_update_term_count = true;
				}
				// カテゴリー登録
				if(!$this->BlogTermLink->saveTermLinks($this->content_id, $this->BlogPost->id, $isBeforeUpdateTermCount, $is_after_update_term_count,
					$active_category_arr, 'id', 'category')) {
					$this->flash(__('Failed to register the database, (%s).', 'blog_term_links'), null, 'BlogPosts.index.003', '500');
					return;
				}
				// タグ登録
				if(!$this->BlogTermLink->saveTermLinks($this->content_id, $this->BlogPost->id, $isBeforeUpdateTermCount, $is_after_update_term_count,
						$active_tag_arr, 'name', 'tag')) {
					$this->flash(__('Failed to register the database, (%s).', 'blog_term_links'), null, 'BlogPosts.index.004', '500');
					return;
				}

				if($status == NC_STATUS_PUBLISH) {
					// 決定の場合、メイン画面にリダイレクト
					$backId = 'blog-post' . $this->id. '-' . $this->BlogPost->id;
					$editUrl = array('controller' => 'blog', '#' => $backId);
					if(isset($this->request->query['back_query'])) {
						$editUrl = array_merge($backUrl, explode('/', $this->request->query['back_query']));
					}
					$editUrl['limit'] = isset($this->request->query['back_limit']) ? $this->request->query['back_limit'] : null;
					$editUrl['page'] = isset($this->request->query['back_page']) ? $this->request->query['back_page'] : null;
					$this->redirect($editUrl);
					return;
				} else if(!isset($postId)) {
					// 新規投稿ならば、編集画面リダイレクト
					$this->redirect(array('controller' => 'blog_posts', $this->BlogPost->id, '#' => $this->id));
					return;
				}
			}
		}

		// 履歴情報
		if(isset($blogPost['Revision']['id'])) {
			$this->set('revisions', $this->Revision->findRevisions($blogPost['Revision']['id']));
		}

		$this->set('blog', $blog);
		$this->set('blog_post', $blogPost);

		// カテゴリ一覧、タグ一覧
		$categories = $this->BlogTerm->findCategories($this->content_id, isset($postId) ? $postId : null, $active_category_arr);
		$tags = $this->BlogTerm->findTags($this->content_id, isset($postId) ? $postId : null, $active_tag_arr);
		$this->set('categories', $categories);
		$this->set('tags', $tags);
		$this->set('post_id', $postId);
	}

/**
 * ブログ記事削除
 * @param   integer $postId
 * @return  void
 * @since   v 3.0.0.0
 */
	public function delete($postId = null) {
		if(empty($postId) || !$this->request->is('post')) {
			$this->flash(__('Unauthorized request.<br />Please reload the page.'), null, 'BlogPosts.delete.001', '500');
			return;
		}
		$blogPost = $this->BlogPost->findById($postId);
		if(!isset($blogPost['BlogPost'])) {
			$this->flash(__('Unauthorized request.<br />Please reload the page.'), null, 'BlogPosts.delete.002', '500');
			return;
		}

		// コメント削除
		$delConditions = array('BlogComment.blog_post_id'=>$postId);
		if(!$this->BlogComment->deleteAll($delConditions, false)){
			$this->flash(__('Failed to delete the database, (%s).', 'blog_comments'), null, 'BlogPosts.delete.003', '500');
			return;
		}

		// 一般会員が閲覧できるカウント数のカウントダウン

		if($blogPost['BlogPost']['is_future'] != _ON && $blogPost['BlogPost']['status'] != NC_STATUS_TEMPORARY  && $blogPost['BlogPost']['is_approved'] != NC_DISPLAY_FLAG_OFF){
			$termLinks = $this->BlogTermLink->findAllByBlogPostId($postId);
			if($termLinks){
				// blogに結びつくすべてのtermがカウントダウン対象
				$termIds = array();
				foreach ($termLinks as $key => $termLink){
					array_push($termIds, $termLink['BlogTermLink']['blog_term_id']);
				}
				$cntdown_conditions = array('BlogTerm.id'=>$termIds);
				if(!$this->BlogTerm->decrementSeq($cntdown_conditions, 'count')){
					$this->flash(__('Failed to update the database, (%s).', 'blog_term_links'), null, 'BlogPosts.delete.004', '500');
					return;
				}
			}
		}

		// タームリンク削除
		$delConditions = array('BlogTermLink.blog_post_id'=>$postId);
		if(!$this->BlogTermLink->deleteAll($delConditions)){
			$this->flash(__('Failed to delete the database, (%s).', 'blog_term_links'), null, 'BlogPosts.delete.005', '500');
			return;
		}

		// blog削除
		$delConditions = array('BlogPost.id'=>$postId);
		if(!$this->BlogPost->deleteAll($delConditions)){
			$this->flash(__('Failed to delete the database, (%s).', 'blog_posts'), null, 'BlogPosts.delete.006', '500');
			return;
		}

		// revision削除
		if(!$this->Revision->deleteRevison($blogPost['BlogPost']['revision_group_id'])){
			$this->flash(__('Failed to delete the database, (%s).', 'revisions'), null, 'BlogPosts.delete.007', '500');
			return;
		}

		// リダイレクト
		$this->redirect($this->_getRedirectUrl($this->id, $this->block_id, $blogPost['BlogPost']['content_id'], $this->hierarchy));
	}

/**
 * ブログ記事削除時リダイレクトURL取得
 * 		現在のページ上にほかの記事があれば、そのページへ
 * 		なければ、1ペー目へリダイレクト
 * @param   integer $id
 * @param   integer $blockId
 * @param   integer $contentId
 * @param   integer $hierarchy
 * @return  array $redirectUrl
 * @since   v 3.0.0.0
 */
	protected function _getRedirectUrl($id, $blockId, $contentId, $hierarchy) {
		$userId = $this->Auth->user('id');
		$redirectUrl = array('controller' => 'blog', 'action'=> 'index', '#' => $id);
		$joins = array();
		$page = isset($this->request->query['back_page']) ? intval($this->request->query['back_page']) : 1;
		if(isset($this->request->query['back_query'])) {
			$redirectUrl = array_merge($redirectUrl, explode('/', $this->request->query['back_query']));
			if(isset($redirectUrl[0])) {
				$requestConditions = array();
				if(preg_match('/[0-9]+/', $redirectUrl[0])) {
					$requestConditions = array('year' => $redirectUrl[0]);
					if(isset($redirectUrl[1])) {
						$requestConditions = array('month' => $redirectUrl[1]);
					}
					if(isset($redirectUrl[2])) {
						$requestConditions = array('day' => $redirectUrl[2]);
					}
					if(isset($redirectUrl[3])) {
						$requestConditions = array('subject' => $redirectUrl[3]);
					}
				} else if(isset($redirectUrl[1])) {
					switch($redirectUrl[0]) {
						case 'author':
						case 'tag':
						case 'category':
						case 'keyword':
							$requestConditions = array($redirectUrl[0] => $redirectUrl[1]);
							break;
					}
				}
				if(count($requestConditions) > 0) {
					list($addParams, $joins) = $this->BlogPost->getPaginateConditions($requestConditions);
				}
			}
		}
		if(isset($page) && $page > 1) {
			$limit = $redirectUrl['limit'] = isset($this->request->query['back_limit']) ? $this->request->query['back_limit'] : null;
			if(!isset($limit)) {
				$params = array(
					'fields' => array('BlogStyle.visible_item'),
					'conditions' => array('BlogStyle.block_id' => $blockId, 'BlogStyle.widget_type' => BLOG_WIDGET_TYPE_MAIN),
					'order' => null,
				);
				$blog_style = $this->BlogStyle->find('first', $params);
				if(isset($blog_style['BlogStyle'])) {
					$limit = $blog_style['BlogStyle']['visible_item'];
				} else {
					$limit = BLOG_DEFAULT_VISIBLE_ITEM;
				}
			}
			$conditions = $this->BlogPost->getConditions($contentId, $userId, $hierarchy);
			if(isset($addParams)) {
				$conditions = array_merge($conditions, $addParams);
			}
			$redirectBlogPosts = $this->BlogPost->find('all', array(
				'fields' => array('BlogPost.id'),
				'conditions' => $conditions,
				'joins' => $joins,
				'page' => $page,
				'limit' => $limit,
				'recursive' => -1
			));
			if(count($redirectBlogPosts) > 0) {
				$redirectUrl['page'] = $page;
			} else if($page > 2) {
				// 1ページ前を表示
				$redirectUrl['page'] = $page - 1;
			}
		}
		return $redirectUrl;
	}

/**
 * 履歴情報表示
 * @param   integer $blogPostId
 * @return  void
 * @since   v 3.0.0.0
 */
	public function revision($blogPostId) {
		$blogPost = $this->BlogPost->findById($blogPostId);
		if(!isset($blogPost['BlogPost'])) {
			$this->flash(__('Content not found.'), null, 'BlogPosts.revision.001', '404');
			return;
		}
		// 自動保存で最新のデータがあった場合、表示
		$revision = $this->Revision->findRevisions(null, $blogPost['Revision']['group_id'], 1);
		if(isset($revision[0])) {
			$blogPost['Revision'] = $revision[0]['Revision'];
		} else {
			$blogPost['Revision'] = array('content' => '');
		}
		$cancelUrl = array('action' => 'index', $blogPostId, '#' => $this->id);

		$ret = $this->RevisionList->setDatas($blogPost['BlogPost']['title'], $blogPost, array($blogPostId), $cancelUrl);
		if($ret === false) {
			$this->flash(__('Content not found.'), null, 'BlogPosts.revision.002', '404');
			return;
		}

		if($this->request->is('post')) {
			// TODO:復元のバリデートでそのrevision番号が本当に戻せるかどうか確認すること auto-drafutのデータ等
			$this->redirect($cancelUrl);
			return;
		}
		$this->render('/Revisions/index');
	}
}