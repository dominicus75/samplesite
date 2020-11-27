<?php
/*
 * @file ViewInterface.php
 * @package MVC
 * @copyright 2020 Domokos Endre János <domokos.endrejanos@gmail.com>
 * @license MIT License (https://opensource.org/licenses/MIT)
 */

namespace Dominicus75\MVC;

interface ViewInterface {

  public function __construct(
    string $templateDirectory,
    string $templateFile,
    array $templateVariables
  );

  public function getName(): string;

  public function getTemplate(): string;

  public function getTemplateVariables(): array;

  public function render(): string;

  public function display(): void;

}
