<?php
/*
 * @file ControllerInterface.php
 * @package MVC
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\MVC;

interface ControllerInterface {

  public function __construct(
    Object $request,
    string $action,
    string $contentId
  );

  public function getName(): string;

  public function getRequest(): Object;

  public function getAction(): string;

  public function getContentId(): string;

  public function getView(): ViewInterface;

  public function getModel(): ModelInterface;

}
