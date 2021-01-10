<?php
/*
 *
 * @package Validator
 * @author Domokos Endre János <domokos.endrejanos@gmail.com>
 * @copyright 2020 Domokos Endre János
 * @license GNU General Public License v3 (https://opensource.org/licenses/GPL-3.0)
 *
 *
 */

namespace Dominicus75\Validator;

class Pattern
{

  const TEXT   = "/^([\w\s\-\?\!\:\.\,\;\/\„\”\"\`\´\'\˝\»\«\^\*\+\%=\×\÷\±§#@&©®~\{\}\(\)\[\]]{1,10240})$/iu";
  const QUERY  = "/^([a-zA-Z0-9_\-\&\=\.\/]{10,128})$/i";
  const HTML   = "/(?P<opentag><(?P<name>[\w]+[^>]*)>).*?(?P<closetag><\/(?P=name)>)/iu";
  const SCRIPT = "/<script\b[^>]*>(.*?)<\/script\b[^>]*>/isu";
  const PHP    = "/(<+\?(php|\=)(.*?)\?>+)/iu";
  const EMAIL  = "/^[-a-z0-9\.]{1,30}@[-a-z0-9\.]{3,30}\.[a-z]{2,6}$/i";

  const ALLOWED_TAGS = '<span><h2><h3><h4><h5><h6><p><blockquote><cite><br>'
    .'<strong><b><em><i><underline><u><strike><s><del>'
    .'<sub><sup><ol><ul><li><dl><dt><dd><figure><figcaption><img>'
    .'<iframe><video><a><table><thead><caption><tbody><tfoot><tr><td>';

}
