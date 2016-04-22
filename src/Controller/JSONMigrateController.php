<?php

/**
 * @file
 * Contains \Drupal\json_migrate\Controller\JSONMigrateController.
 */

namespace Drupal\json_migrate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Class JSONMigrateController.
 *
 * @package Drupal\json_migrate\Controller
 */
class JSONMigrateController extends ControllerBase {

  /**
   * Returns a link from a route.
   * @param $route
   * @param $label
   * @return mixed
   */
  private function createLinkFromRoute($route, $label)
  {
    $url = Url::fromRoute($route);
    $link = Link::fromTextAndUrl($label, $url);
    $link = $link->toRenderable();
    $link['#attributes'] = array('class' => array('admin'));
    $output = render($link);
    return $output;
  }

  /**
   * Returns the administration links list.
   * @return mixed
   */
  private function getAdminLinksList()
  {
    $items = [];
    $items[] = $this->createLinkFromRoute('json_migrate.select_content_type_form',
      $this->t('Import nodes by content types.'));
    $items[] = $this->createLinkFromRoute('json_migrate.select_vocabulary_form',
      $this->t('Import terms by vocabulary.'));

    $list['admin-links-list'] = array(
      '#theme' => 'item_list',
      '#items' => $items,
      '#type' => 'ul',
    );
    return $list;
  }

  /**
   * Returns the documentation link.
   * @return mixed
   */
  public static function getDocumentationLink()
  {
    $path = 'https://github.com/r-daneelolivaw/json_migrate';
    $url = Url::fromUri($path);
    $link = Link::fromTextAndUrl(t('Documentation'), $url);
    $link = $link->toRenderable();
    $link['#attributes'] = array('class' => array('documentation'));
    $output = render($link);
    return $output;
  }

  /**
   * Admin overview.
   *
   * @return string
   *   Return Hello string.
   */
  public function adminOverview() {
    $list = $this->getAdminLinksList();
    return [
        '#type' => 'markup',
        '#markup' => render($list),
    ];
  }

}
