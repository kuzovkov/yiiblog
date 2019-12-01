<?php

namespace {

  class JSLObject implements ArrayAccess
  {

    protected $arr = null;

    public function __construct()
    {
      $this->arr = array();
    }

    public function offsetSet($offset, $value)
    {
      if (!is_null($offset) && strlen($offset) > 0)
      {
        if ($this->isPublicProperty($offset))
        {
          $this->$offset = $value;
        }
        else
        {
          $this->arr[$offset] = $value;
        }
      }
      else
      {
        throw new Exception("Property name is null or an empty string!");
      }
    }

    public function offsetExists($offset)
    {
      $r = false;
      if (!is_null($offset) && strlen($offset) > 0)
      {
        $r = array_key_exists($offset, $this->arr) && isset($this->arr[$offset]) || $this->isPublicProperty($offset);
      }
      return $r;
    }

    public function offsetUnset($offset)
    {
      if (!is_null($offset) && strlen($offset) > 0)
      {
        if ($this->isPublicProperty($offset))
        {
          unset($this->$offset);
        }
        else
        if (array_key_exists($offset, $this->arr))
        {
          unset($this->arr[$offset]);
        }
      }
    }

    public function offsetGet($offset)
    {
      $r = null;
      if (!is_null($offset) && strlen($offset) > 0)
      {
        if ($this->isPublicProperty($offset))
        {
          $r = $this->$offset;
        }
        else
        if (array_key_exists($offset, $this->arr))
        {
          $r = $this->arr[$offset];
        }
      }
      return $r;
    }

    public function getPropertyNames()
    {
      $a = array_keys($this->arr);
      $keys = new JSLArray();
      $len = count($a);
      for ($i = 0; $i < $len; $i++)
      {
        $keys->push($a[$i]);
      }
      $reflection = new ReflectionObject($this);
      $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
      $len = count($properties);
      for ($i = 0; $i < $len; $i++)
      {
        $po = $properties[$i];
        $keys->push($po->getName());
      }
      return $keys;
    }

    public function hasProperty($property)
    {
      $r = false;
      if (!is_null($property) && strlen($property) > 0)
      {
        $r = array_key_exists($property, $this->arr) || $this->isPublicProperty($property);
      }
      return $r;
    }

    protected function isPublicProperty($property)
    {
      $r = false;
      if (property_exists($this, $property))
      {
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $pc = count($properties);
        $i = 0;
        while (!$r && $i < $pc)
        {
          $po = $properties[$i];
          $r = $po->getName() == $property;
          $i++;
        }
      }
      return $r;
    }

    function __get($property)
    {
      $r = $this->offsetGet($property);
      return $r;
    }

    function __set($property, $value)
    {
      $this->offsetSet($property, $value);
      return $value;
    }

  }

  class JSLArray extends JSLObject implements Countable
  {

    public function __construct()
    {
      $args = func_get_args();
      if (isset($args) && count($args) > 0)
      {
        if (count($args) == 1)
        {
          $arg = $args[0];
          if (is_int($arg) && $arg > 0)
          {
            $this->arr = array_fill(0, $arg, null);
          }
          else
          {
            $this->arr = array();
            $this->arr[] = $arg;
          }
        }
        else
        {
          $this->arr = array_slice($args, 0);
        }
      }
      else
      {
        $this->arr = array();
      }
    }

    protected function &getMergedArray($args)
    {
      $r = null;
      $arrays = array();
      $arrays[] = &$this->arr;
      $len = count($args);
      for ($i = 0; $i < $len; $i++)
      {
        $arg = &$args[$i];
        if (isset($arg))
        {
          if (is_array($arg))
          {
            $arrays[] = &$arg;
          }
          else
          if ($arg instanceof JSLArray)
          {
            $arrays[] = &$arg->arr;
          }
          else
          {
            $tmp = array();
            $tmp[] = &$arg;
            $arrays[] = $tmp;
          }
        }
      }
      $r = call_user_func_array('array_merge', $arrays);
      return $r;
    }

    public function __toString()
    {
      $r = null;
      if (count($this->arr) > 0)
      {
        $r = "[" . implode(',', $this->arr) . "]";
      }
      else
      {
        $r = "[]";
      }
      return $r;
    }

    public function offsetSet($offset, $value)
    {
      if (is_null($offset))
      {
        $this->arr[] = $value;
      }
      else
      {
        parent::offsetSet($offset, $value);
      }
    }

    public function join($separator = ',')
    {
      return implode($separator, $this->arr);
    }

    public function concat()
    {
      $a = func_get_args();
      $r = new self();
      if (isset($a) && count($a) > 0)
      {
        $r->arr = &$this->getMergedArray($a);
      }
      else
      {
        $r->arr = clone $this->arr;
      }
      return $r;
    }

    public function indexOf($element, $fromIndex = 0)
    {
      $r = -1;
      if ($fromIndex == 0)
      {
        $r = array_search($element, $this->arr, true);
        if ($r === false)
        {
          $r = -1;
        }
      }
      else
      {
        $len = count($this->arr);
        $i = $fromIndex < 0 ? 0 : $fromIndex;
        while ($r == -1 && $i < $len)
        {
          $tmp = array_key_exists($i, $this->arr) ? $this->arr[$i] : null;
          if (isset($tmp) && isset($element) && $element === $tmp || !isset($tmp) && !isset($element))
          {
            $r = $i;
          }
          $i++;
        }
      }
      return $r;
    }

    public function lastIndexOf($element, $fromIndex = null)
    {
      $r = -1;
      if (count($this->arr) > 0)
      {
        if (isset($fromIndex))
        {
          if ($fromIndex > count($this->arr) - 1)
          {
            $fi = count($this->arr) - 1;
          }
          else
          {
            $fi = $fromIndex;
          }
        }
        else
        {
          $fi = count($this->arr) - 1;
        }
        $i = $fi;
        while ($r == -1 && $i >= 0)
        {
          $tmp = array_key_exists($i, $this->arr) ? $this->arr[$i] : null;
          if (isset($tmp) && isset($element) && $element === $tmp || !isset($tmp) && !isset($element))
          {
            $r = $i;
          }
          $i--;
        }
      }
      return $r;
    }

    public function pop()
    {
      $r = array_pop($this->arr);
      return $r;
    }

    public function push()
    {
      $args = func_get_args();
      if (isset($args) && count($args) > 0)
      {
        $len = count($args);
        for ($i = 0; $i < $len; $i++)
        {
          array_push($this->arr, $args[$i]);
        }
      }
      return count($this->arr);
    }

    public function append($jslArray)
    {
      if (isset($jslArray) && count($jslArray) > 0)
      {
        $len = count($jslArray);
        for ($i = 0; $i < $len; $i++)
        {
          array_push($this->arr, $jslArray[$i]);
        }
      }
      return count($this->arr);
    }

    public function appendNative($nativeArray)
    {
      if (isset($nativeArray) && count($nativeArray) > 0)
      {
        $len = count($nativeArray);
        for ($i = 0; $i < $len; $i++)
        {
          array_push($this->arr, $nativeArray[$i]);
        }
      }
      return count($this->arr);
    }

    public function shift()
    {
      $r = array_shift($this->arr);
      return $r;
    }

    public function unshift()
    {
      array_splice($this->arr, 0, 0, func_get_args());
      return count($this->arr);
    }

    public function sort()
    {
      $args = func_get_args();
      if (isset($args) && count($args) > 0)
      {
        usort($this->arr, $args[0]);
      }
      else
      {
        sort($this->arr);
      }
      return $this;
    }

    public function reverse()
    {
      rsort($this->arr);
      return $this;
    }

    public function slice()
    {
      $array = new self();
      $args = func_get_args();
      $argsCount = isset($args) ? count($args) : 0;
      $begin = $argsCount > 0 ? $args[0] : 0;
      $end = $argsCount > 1 ? $args[1] : count($this->arr);
      $a = array_slice($this->arr, $begin, $end - $begin);
      $array->arr = isset($a) ? $a : array();
      return $array;
    }

    public function splice()
    {
      $array = new self();
      $args = func_get_args();
      $argsCount = isset($args) ? count($args) : 0;
      $start = $argsCount > 0 ? $args[0] : 0;
      $deleteCount = $argsCount > 1 ? $args[1] : count($this->arr) - $start;
      $replacement = null;
      if ($argsCount > 2)
      {
        $replacement = array_slice($args, 2);
      }
      else
      {
        $replacement = array();
      }
      $a = array_splice($this->arr, $start, $deleteCount, $replacement);
      $array->arr = isset($a) ? $a : array();
      return $array;
    }

    public function nativeArray()
    {
      return array_slice($this->arr, 0);
    }

    public function forEach_($callback)
    {
      foreach ($this->arr as $index => $value)
      {
        call_user_func_array($callback, array($value, $index, &$this->arr));
      }
      return $this;
    }

    public function filter($callback)
    {
      $array = new self();
      foreach ($this->arr as $index => $value)
      {
        if (call_user_func_array($callback, array($value, $index, &$this->arr)))
        {
          $array->push($value);
        }
      }
      return $array;
    }

    public function map($callback)
    {
      $array = new self();
      foreach ($this->arr as $index => $value)
      {
        $array->push(call_user_func_array($callback, array($value, $index, &$this->arr)));
      }
      return $array;
    }

    public function count()
    {
      return count($this->arr);
    }

  }

}


namespace com\cc\odbd\ret\model {

  use \SimpleXMLElement;
  
  interface IXMLAppendable
  {

    public function appendXML($parent);
  }

  class ExportedModelItem
  {

    public function __construct()
    {
      
    }

    protected $name_ = null;

    public function setName($value)
    {
      $this->name_ = $value;
    }

    public function getName()
    {
      return $this->name_;
    }

    protected $comment_ = null;

    public function setComment($value)
    {
      $this->comment_ = $value;
    }

    public function getComment()
    {
      return $this->comment_;
    }

    protected function setProperties($xml)
    {
      if (isset($this->name_))
      {
        $xml->addAttribute("name", $this->name_);
      }
      if (isset($this->comment_) && strlen($this->comment_) > 0)
      {
        $xml->comment = $this->comment_;
      }
    }

  }

  class ExportedCatalog extends ExportedModelItem implements IXMLAppendable
  {

    public function __construct()
    {
      parent::__construct();
    }

    // JSLArray of ExportedSchema objects

    protected $schemas_ = null;

    public function setSchemas($value)
    {
      $this->schemas_ = $value;
    }

    public function getSchemas()
    {
      return $this->schemas_;
    }

    public function appendXML($parent)
    {
      $r = $parent->addChild("exportedCatalog");
      $this->setProperties($r);
      if (isset($this->schemas_))
      {
        $len = count($this->schemas_);
        for ($i = 0; $i < $len; $i++)
        {
          $s = $this->schemas_[$i];
          $s->appendXML($r);
        }
      }
    }

  }

  class ExportedColumn extends ExportedModelItem implements IXMLAppendable
  {

    public function __construct()
    {
      parent::__construct();
    }

    // String
    protected $type_ = null;

    public function setType($value)
    {
      $this->type_ = $value;
    }

    public function getType()
    {
      return $this->type_;
    }
    
    // String
    protected $fullType_ = null;

    public function setFullType($value)
    {
      $this->fullType_ = $value;
    }

    public function getFullType()
    {
      return $this->fullType_;
    }

    // int
    protected $length_ = 0;

    public function setLength($value)
    {
      $this->length_ = $value;
    }

    public function getLength()
    {
      return $this->length_;
    }

    // int
    protected $precision_ = 0;

    public function setPrecision($value)
    {
      $this->precision_ = $value;
    }

    public function getPrecision()
    {
      return $this->precision_;
    }

    // int
    protected $scale_ = 0;

    public function setScale($value)
    {
      $this->scale_ = $value;
    }

    public function getScale()
    {
      return $this->scale_;
    }

    // String
    protected $defaultValue_ = null;

    public function setDefaultValue($value)
    {
      $this->defaultValue_ = $value;
    }

    public function getDefaultValue()
    {
      return $this->defaultValue_;
    }

    // Boolean
    protected $nullable_ = false;

    public function setNullable($value)
    {
      $this->nullable_ = $value;
    }

    public function isNullable()
    {
      return $this->nullable_;
    }

    // Boolean
    protected $autoIncrement_ = false;

    public function setAutoIncrement($value)
    {
      $this->autoIncrement_ = $value;
    }

    public function isAutoIncrement()
    {
      return $this->autoIncrement_;
    }

    public function appendXML($parent)
    {
      $r = $parent->addChild("exportedColumn");
      $this->setProperties($r);
    }

    protected function setProperties($xml)
    {
      parent::setProperties($xml);
      if (isset($this->type_))
      {
        $xml->addAttribute('type', $this->type_);
      }
      $xml->addAttribute('length', isset($this->length_) ? strval($this->length_) : "0");
      $xml->addAttribute('precision', isset($this->precision_) ? strval($this->precision_) : "0");
      $xml->addAttribute('scale', isset($this->scale_) ? strval($this->scale_) : "0");
      if (isset($this->defaultValue_))
      {
        $xml->addAttribute('defaultValue', $this->defaultValue_);
      }
      $xml->addAttribute('nullable', $this->nullable_ ? "true" : "false");
      $xml->addAttribute('autoIncrement', $this->autoIncrement_ ? "true" : "false");
    }

  }

  class ExportedReference implements IXMLAppendable
  {

    public function __construct()
    {
      
    }

    // String
    protected $column_ = null;

    public function setColumn($value)
    {
      $this->column_ = $value;
    }

    public function getColumn()
    {
      return $this->column_;
    }

    // String
    protected $referencedColumn_ = null;

    public function setReferencedColumn($value)
    {
      $this->referencedColumn_ = $value;
    }

    public function getReferencedColumn()
    {
      return $this->referencedColumn_;
    }

    public function appendXML($parent)
    {
      $r = $parent->addChild("exportedReference");
      $this->setProperties($r);
    }

    protected function setProperties($xml)
    {
      if (isset($this->column_))
      {
        $xml->addAttribute('column', $this->column_);
      }
      if (isset($this->referencedColumn_))
      {
        $xml->addAttribute('referencedColumn', $this->referencedColumn_);
      }
    }

  }

  class ExportedForeignKey extends ExportedModelItem implements IXMLAppendable
  {

    public function __construct()
    {
      parent::__construct();
    }

    // String
    protected $referencedSchema_ = null;

    public function setReferencedSchema($value)
    {
      $this->referencedSchema_ = $value;
    }

    public function getReferencedSchema()
    {
      return $this->referencedSchema_;
    }

    // String
    protected $referencedTable_ = null;

    public function setReferencedTable($value)
    {
      $this->referencedTable_ = $value;
    }

    public function getReferencedTable()
    {
      return $this->referencedTable_;
    }

    // String
    protected $onUpdateReferentialAction_ = null;

    public function setOnUpdateReferentialAction($value)
    {
      $this->onUpdateReferentialAction_ = $value;
    }

    public function getOnUpdateReferentialAction()
    {
      return $this->onUpdateReferentialAction_;
    }

    // String
    protected $onDeleteReferentialAction_ = null;

    public function setOnDeleteReferentialAction($value)
    {
      $this->onDeleteReferentialAction_ = $value;
    }

    public function getOnDeleteReferentialAction()
    {
      return $this->onDeleteReferentialAction_;
    }

    /*
     * JSLArray of ExportedReference objects.
     */

    protected $references_ = null;

    public function setReferences($value)
    {
      $this->references_ = $value;
    }

    public function getReferences()
    {
      return $this->references_;
    }

    public function appendXML($parent)
    {
      $r = $parent->addChild("exportedForeignKey");
      $this->setProperties($r);
      if (isset($this->references_))
      {
        $len = count($this->references_);
        for ($i = 0; $i < $len; $i++)
        {
          $ref = $this->references_[$i];
          $ref->appendXML($r);
        }
      }
    }

    protected function setProperties($xml)
    {
      parent::setProperties($xml);
      if (isset($this->referencedSchema_))
      {
        $xml->addAttribute('referencedSchema', $this->referencedSchema_);
      }
      if (isset($this->referencedTable_))
      {
        $xml->addAttribute('referencedTable', $this->referencedTable_);
      }
      if (isset($this->onUpdateReferentialAction_))
      {
        $xml->addAttribute('onUpdateReferentialAction', $this->onUpdateReferentialAction_);
      }
      if (isset($this->onDeleteReferentialAction_))
      {
        $xml->addAttribute('onDeleteReferentialAction', $this->onDeleteReferentialAction_);
      }
    }

  }

  class ExportedIndexedColumn implements IXMLAppendable
  {

    public function __construct()
    {
      
    }

    // String
    protected $column_ = null;

    public function setColumn($value)
    {
      $this->column_ = $value;
    }

    public function getColumn()
    {
      return $this->column_;
    }

    // String
    protected $order_ = null;

    public function setOrder($value)
    {
      $this->order_ = $value;
    }

    public function getOrder()
    {
      return $this->order_;
    }

    // int
    protected $prefixLength_ = 0;

    public function setPrefixLength($value)
    {
      $this->prefixLength_ = $value;
    }

    public function getPrefixLength()
    {
      return $this->prefixLength_;
    }

    public function appendXML($parent)
    {
      $r = $parent->addChild("exportedIndexedColumn");
      $this->setProperties($r);
    }

    protected function setProperties($xml)
    {
      if (isset($this->column_))
      {
        $xml->addAttribute('column', $this->column_);
      }
      if (isset($this->order_))
      {
        $xml->addAttribute('order', $this->order_);
      }
      if (isset($this->prefixLength_))
      {
        $xml->addAttribute('prefixLength', strval($this->prefixLength_));
      }
    }

  }

  class ExportedIndex extends ExportedModelItem implements IXMLAppendable
  {

    public function __construct()
    {
      parent::__construct();
    }

    // Boolean
    protected $unique_ = false;

    public function setUnique($value)
    {
      $this->unique_ = $value;
    }

    public function isUnique()
    {
      return $this->unique_;
    }

    // Boolean
    protected $clustered_ = false;

    public function setClustered($value)
    {
      $this->clustered_ = $value;
    }

    public function isClustered()
    {
      return $this->clustered_;
    }

    /*
     * JSLArray of ExportedIndexedColumn objects.
     */

    protected $indexedColumns_ = null;

    public function setIndexedColumns($value)
    {
      $this->indexedColumns_ = $value;
    }

    public function getIndexedColumns()
    {
      return $this->indexedColumns_;
    }

    public function appendXML($parent)
    {
      $r = $parent->addChild("exportedIndex");
      $this->setProperties($r);
      if (isset($this->indexedColumns_))
      {
        $len = count($this->indexedColumns_);
        for ($i = 0; $i < $len; $i++)
        {
          $ic = $this->indexedColumns_[$i];
          $ic->appendXML($r);
        }
      }
      return $r;
    }

    protected function setProperties($xml)
    {
      parent::setProperties($xml);
      $xml->addAttribute('unique', $this->unique_ ? "true" : "false");
      $xml->addAttribute('clustered', $this->clustered_ ? "true" : "false");
    }

  }

  class ExportedPrimaryKey extends ExportedModelItem implements IXMLAppendable
  {

    public function __construct()
    {
      parent::__construct();
    }

    /*
     * JSLArray of column names (String).
     */

    protected $columns_ = null;

    public function setColumns($value)
    {
      $this->columns_ = $value;
    }

    public function getColumns()
    {
      return $this->columns_;
    }

    public function appendXML($parent)
    {
      $r = $parent->addChild("exportedPrimaryKey");
      $this->setProperties($r);
      if (isset($this->columns_))
      {
        $len = count($this->columns_);
        for ($i = 0; $i < $len; $i++)
        {
          $xml = $r->addChild("column");
          $xml->addAttribute("name", $this->columns_[$i]);
        }
      }
      return $r;
    }

  }

  class ExportedTable extends ExportedModelItem implements IXMLAppendable
  {

    public function __construct()
    {
      parent::__construct();
    }

    /*
     * JSLArray of ExportedColumn objects.
     */

    protected $columns_ = null;

    public function setColumns($value)
    {
      $this->columns_ = $value;
    }

    public function getColumns()
    {
      return $this->columns_;
    }

    public function getColumn($columnName)
    {
      $r = null;
      if(isset($this->columns_))
      {
        $c = count($this->columns_);
        $i = 0;
        while(!isset($r) && $i<$c)
        {
          $column = $this->columns_[$i];
          $cn = $column->getName();
          if($cn==$columnName)
          {
            $r = $column;
          }
          $i++;
        }
      }
      return $r;
    }
    
    // ExportedPrimaryKey
    protected $primaryKey_ = null;

    public function setPrimaryKey($value)
    {
      $this->primaryKey_ = $value;
    }

    public function getPrimaryKey()
    {
      return $this->primaryKey_;
    }
    
    public function isPrimaryKeyColumn($columnName)
    {
      $r = false;
      if(isset($this->primaryKey_))
      {
        $cs = $this->primaryKey_->getColumns();
        $c = count($cs);
        $i = 0;
        while(!$r && $i < $c)
        {
          $r = $columnName == $cs[$i];
          $i++;
        }
      }
      return $r;
    }

    /*
     * JSLArray of ExportedForeignKey objects.
     */

    protected $foreignKeys_ = null;

    public function setForeignKeys($value)
    {
      $this->foreignKeys_ = $value;
    }

    public function getForeignKeys()
    {
      return $this->foreignKeys_;
    }

    public function getForeignKeyByColumn($columnName)
    {
      $r = null;
      if(isset($this->foreignKeys_))
      {
        $fks = $this->foreignKeys_;
        $c = count($fks);
        $i = 0;
        while(!isset($r) && $i < $c)
        {
          $fk = $fks[$i];
          $refs = $fk->getReferences();
          $cRef = count($refs);
          $iRef = 0;
          while(!isset($r) && $iRef < $cRef)
          {
            $ref = $refs[$iRef];
            $col = $ref->getColumn();
            if($col==$columnName)
            {
              $r = $fk;
            }
            $iRef++;
          }
          $i++;
        }
      }
      return $r;
    }
    
    /*
     * JSLArray of ExportedIndex objects.
     */

    protected $indexes_ = null;

    public function setIndexes($value)
    {
      $this->indexes_ = $value;
    }

    public function getIndexes()
    {
      return $this->indexes_;
    }

    public function appendXML($parent)
    {
      $r = $parent->addChild("exportedTable");
      $this->setProperties($r);
      if (isset($this->columns_))
      {
        $len = count($this->columns_);
        for ($i = 0; $i < $len; $i++)
        {
          $c = $this->columns_[$i];
          $c->appendXML($r);
        }
      }
      if (isset($this->primaryKey_))
      {
        $this->primaryKey_->appendXML($r);
      }
      if (isset($this->foreignKeys_))
      {
        $len = count($this->foreignKeys_);
        for ($i = 0; $i < $len; $i++)
        {
          $fk = $this->foreignKeys_[$i];
          $fk->appendXML($r);
        }
      }
      if (isset($this->indexes_))
      {
        $len = count($this->indexes_);
        for ($i = 0; $i < $len; $i++)
        {
          $ix = $this->indexes_[$i];
          $ix->appendXML($r);
        }
      }
      return $r;
    }

  }

  class ExportedSchema extends ExportedModelItem implements IXMLAppendable
  {

    public function __construct()
    {
      parent::__construct();
    }

    // JSLArray of ExportedTable objects.
    protected $tables_ = null;

    public function setTables($value)
    {
      $this->tables_ = $value;
    }

    public function getTables()
    {
      return $this->tables_;
    }

    public function appendXML($parent)
    {
      $r = $parent->addChild("exportedSchema");
      $this->setProperties($r);
      if (isset($this->tables_))
      {
        $len = count($this->tables_);
        for ($i = 0; $i < $len; $i++)
        {
          $t = $this->tables_[$i];
          $t->appendXML($r);
        }
      }
      return $r;
    }
    
    public function getTable($tableName)
    {
      $r = null;
      if(isset($this->tables_))
      {
        $ts = $this->tables_;
        $c = count($ts);
        $i = 0;
        while(!isset($r) && $i < $c)
        {
          $t = $ts[$i];
          $tn = $t->getName();
          if($tn==$tableName)
          {
            $r = $t;
          }
          $i++;
        }
      }
      return $r;
    }

  }

  class ExportedModel
  {

    public function __construct()
    {
      
    }

    protected $dbms_ = null;

    public function setDBMS($value)
    {
      $this->dbms_ = $value;
    }

    public function getDBMS()
    {
      return $this->dbms_;
    }

    protected $versionNumber_ = null;

    public function setVersionNumber($value)
    {
      $this->versionNumber_ = $value;
    }

    public function getVersionNumber()
    {
      return $this->versionNumber_;
    }
    
    // JSLArray of ExportedCatalog objects.
    protected $catalogs_ = null;

    public function setCatalogs($value)
    {
      $this->catalogs_ = $value;
    }

    public function getCatalogs()
    {
      return $this->catalogs_;
    }

    public function toXML()
    {
      $r = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><exportedModel></exportedModel>');
      $r->addAttribute("dbms", $this->dbms_);
      $r->addAttribute("versionNumber", strval($this->versionNumber_));
      if (isset($this->catalogs_))
      {
        $len = count($this->catalogs_);
        for ($i = 0; $i < $len; $i++)
        {
          $c = $this->catalogs_[$i];
          $c->appendXML($r);
        }
      }
      return $r;
    }

  }

}


namespace com\cc\odbd\ret\dbms {

  use \mysqli;
  use \JSLArray;
  use \com\cc\odbd\ret\model\ExportedCatalog;
  use \com\cc\odbd\ret\model\ExportedColumn;
  use \com\cc\odbd\ret\model\ExportedForeignKey;
  use \com\cc\odbd\ret\model\ExportedIndex;
  use \com\cc\odbd\ret\model\ExportedIndexedColumn;
  use \com\cc\odbd\ret\model\ExportedModel;
  use \com\cc\odbd\ret\model\ExportedPrimaryKey;
  use \com\cc\odbd\ret\model\ExportedReference;
  use \com\cc\odbd\ret\model\ExportedSchema;
  use \com\cc\odbd\ret\model\ExportedTable;

  abstract class DBMS
  {

    abstract public function connect($hostname, $port, $database, $user, $password, $charSet);

    abstract public function disconnect();
    
    abstract public function escapeString($string);

    /*
     *  $catalogFilter: array of strings (names)
     *  $schemaFilter: array of associative arrays: array{"catalog" => "...", "schema" => "...")
     *  $tableFilter: array of associative arrays: array{"catalog" => "...", "schema" => "...", "table" => "...")
     */
    abstract public function getExportedModel($catalogFilter = null, $schemaFilter = null, $tableFilter = null);
    
    abstract public function executeQuery($sql);
    
    abstract public function executeUpdate($sql);
    
    protected function matchFilter($filter, $array)
    {
      $r = false;
      $len = count($filter);
      $i = 0;
      while(!$r && $i<$len)
      {
        $fv = $filter[$i];
        $keys = array_keys($fv);
        $kLen = count($fv);
        $j = 0;
        $match = true;
        while($match && $j<$kLen)
        {
          $key = $keys[$j];
          $match = $fv[$key]==$array[$key];
          $j++;
        }
        $r = $match;
        $i++;
      }
      return $r;
    }
    
    abstract public function getFullType($exportedColumn);
    
    abstract public function hasLength($type);

    abstract public function hasPrecision($type);

    abstract public function hasScale($type);

    abstract public function isDateTimeType($type);

    abstract public function isDateType($type);

    abstract public function isTimeType($type);

    abstract public function isStringType($type);

    abstract public function isFixedPointNumericType($type);

    abstract public function isNumericType($type);

    abstract public function getNumericMinValueAsString($type, $precision, $scale);

    abstract public function getNumericMaxValueAsString($type, $precision, $scale);

    abstract public function isIntegerType($type);
  }

  class MySQL extends DBMS
  {

    protected $mysql = null;
    protected $oldMysqlInterfaceUsed = false;
    protected $versionNumber = null;

    public function connect($hostname, $port, $database, $user, $password, $charSet)
    {
      $r = null;
      $this->oldMysqlInterfaceUsed = function_exists("mysql_pconnect");
      if ($this->oldMysqlInterfaceUsed)
      {
        $this->mysql = mysql_pconnect($hostname.":".$port, $user, $password);
        if ($this->mysql !== false)
        {
          if (mysql_select_db($database, $this->mysql) === false)
          {
            $r = mysql_error();
          }
          else
          {
            if (mysql_query("SET NAMES ".$charSet, $this->mysql) === false)
            {
              $r = mysql_error();
            }
          }
        }
        else
        {
          $r = mysql_error();
        }
      }
      else
      {
        $this->mysql = new mysqli($hostname, $user, $password, $database, $port);
        $connectError = $this->mysql->connect_error;
        if (!isset($connectError))
        {
          $this->mysql->set_charset(charSet);
          $error = $mysql->error;
          if (isset($error))
          {
            $r = $error;
          }
        }
        else
        {
          $r = $connectError;
        }
      }
      if (!isset($r))
      {
        $versionRows = $this->executeQuery("SELECT @@version as version_number");
        if (is_array($versionRows))
        {
          $vn = $versionRows[0]["version_number"];
          $lowerVN = strtolower($vn);
          $mariadb = strpos($lowerVN, "mariadb");
          $vnPart = explode(".", $vn);
          $majorMinor = $vnPart[0] . "." . $vnPart[1];
          if($mariadb)
          {
            if($majorMinor=="10.0")
            {
              $majorMinor = "5.6";
            }
            else
            if($majorMinor=="10.1" || $majorMinor=="10.2" || $majorMinor=="10.3" || $majorMinor=="10.4")
            {
              $majorMinor = "5.7";
            }
          }
          $this->versionNumber = floatval($majorMinor);
        }
        else
        {
          $r = $versionRows;
        }
      }
      return $r;
    }

    public function disconnect()
    {
      if ($this->oldMysqlInterfaceUsed)
      {
        mysql_close($this->mysql);
      }
      else
      {
        $this->mysql->close();
      }
      $this->mysql = null;
    }
    
    public function escapeString($string)
    {
      $r = null;
      if ($this->oldMysqlInterfaceUsed)
      {
        $r = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($string, $this->mysql) : mysql_escape_string($string);
      }
      else
      {
        $r = mysqli_real_escape_string($this->mysql, $string);
      }
      return $r;
    }

    /*
     *  $catalogFilter: array of strings (names)
     *  $schemaFilter: array of associative arrays: array{"catalog" => "...", "schema" => "...")
     *  $tableFilter: array of associative arrays: array{"catalog" => "...", "schema" => "...", "table" => "...")
     */
    public function getExportedModel($catalogFilter = null, $schemaFilter = null, $tableFilter = null)
    {
      $r = null;
      $model = new ExportedModel();
      $model->setDBMS("MySQL");
      $model->setVersionNumber($this->versionNumber);
      
      $cats = new JSLArray();
      $model->setCatalogs($cats);
      $catalogs = $this->executeQuery("SELECT DISTINCT(CATALOG_NAME) AS CATALOG_NAME FROM INFORMATION_SCHEMA.SCHEMATA ORDER BY CATALOG_NAME");
      if (is_array($catalogs))
      {
        $cLen = count($catalogs);
        for ($cI = 0; $cI < $cLen; $cI++)
        {
          $catalog = $catalogs[$cI];
          $catalogName = $catalog["CATALOG_NAME"];
          if(!isset($catalogFilter) || array_search($catalogName, $catalogFilter)!==false)
          {
            $c = new ExportedCatalog();
            $c->setName($catalogName);
            $cats[] = $c;
            $schs = new JSLArray();
            $c->setSchemas($schs);
            $schemas = $this->executeQuery("SELECT * FROM INFORMATION_SCHEMA.SCHEMATA WHERE CATALOG_NAME = '" . $catalogName . "' ORDER BY SCHEMA_NAME");
            if (is_array($schemas))
            {
              $sLen = count($schemas);
              for ($sI = 0; $sI < $sLen; $sI++)
              {
                $schema = $schemas[$sI];
                $schemaName = $schema["SCHEMA_NAME"];
                $match = true;
                if(isset($schemaFilter))
                {
                  $match = $this->matchFilter($schemaFilter, array("catalog" => $catalogName, "schema" => $schemaName));
                }
                if($match)
                {
                  if ($schemaName != "sys" && $schemaName != "performance_schema" && $schemaName != "mysql" && $schemaName != "information_schema")
                  {
                    $s = new ExportedSchema();
                    $tabs = new JSLArray();
                    $s->setTables($tabs);
                    $s->setName($schemaName);
                    $schs[] = $s;

                    $tables = $this->executeQuery("SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_CATALOG = '" . $catalogName . "' AND TABLE_SCHEMA = '" . $schemaName . "' ORDER BY TABLE_NAME");
                    if (is_array($tables))
                    {
                      $tLen = count($tables);
                      for ($tI = 0; $tI < $tLen; $tI++)
                      {
                        $table = $tables[$tI];
                        $tableName = $table["TABLE_NAME"];
                        $match = true;
                        if(isset($tableFilter))
                        {
                          $match = $this->matchFilter($tableFilter, array("catalog" => $catalogName, "schema" => $schemaName, "table" => $tableName));
                        }
                        if($match)
                        {
                          if ($table["TABLE_TYPE"] != "VIEW")
                          {
                            $t = new ExportedTable();
                            $t->setName($tableName);
                            $t->setComment($table["TABLE_COMMENT"]);

                            $cols = new JSLArray();
                            $t->setColumns($cols);
                            $fks = new JSLArray();
                            $t->setForeignKeys($fks);
                            $ixs = new JSLArray();
                            $t->setIndexes($ixs);

                            $tabs[] = $t;

                            // columns...

                            $columns = $this->executeQuery("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_CATALOG = '" . $catalogName . "' AND TABLE_SCHEMA = '" . $schemaName . "' AND TABLE_NAME = '" . $tableName . "' ORDER BY ORDINAL_POSITION");
                            if (is_array($columns))
                            {
                              $coLen = count($columns);
                              for ($coI = 0; $coI < $coLen; $coI++)
                              {
                                $column = $columns[$coI];
                                $co = new ExportedColumn();
                                $columnName = $column["COLUMN_NAME"];
                                $co->setName($columnName);
                                $co->setComment($column["COLUMN_COMMENT"]);
                                $dataType = strtolower($column["DATA_TYPE"]);
                                $type = $dataType . (strpos(strtolower($column["COLUMN_TYPE"]), "unsigned") !== false ? " unsigned" : "");
                                $co->setType($type);
                                $co->setLength($column["CHARACTER_MAXIMUM_LENGTH"]);
                                $precision = null;
                                if ($dataType == "year")
                                {
                                  if (strpos($column["COLUMN_TYPE"], "(") !== false)
                                  {
                                    $precision = intval(substr($column["COLUMN_TYPE"], 5, 1));
                                  }
                                }
                                else
                                {
                                  if ($this->versionNumber >= 5.6)
                                  {
                                    $precision = isset($column["NUMERIC_PRECISION"]) ? $column["NUMERIC_PRECISION"] : $column["DATETIME_PRECISION"];
                                  }
                                  else
                                  {
                                    $precision = $column["NUMERIC_PRECISION"];
                                  }
                                }
                                $co->setPrecision($precision);
                                $co->setScale($column["NUMERIC_SCALE"]);
                                $co->setFullType($this->getFullType($co));

                                $defaultValue = $column["COLUMN_DEFAULT"];
                                if (isset($defaultValue) && strlen($defaultValue) > 0)
                                {
                                  if (strpos($type, "char") === 0 ||
                                          strpos($type, "varchar") === 0)
                                  {
                                    if (strpos($defaultValue, "'") === false && strpos($defaultValue, "'") === false)
                                    {
                                      $defaultValue = "'" . $defaultValue . "'";
                                    }
                                  }
                                  else
                                  if (strpos($type, "date") === 0 ||
                                          strpos($type, "time") === 0)
                                  {
                                    if ($defaultValue[0] >= '0' && $defaultValue[0] <= '9')
                                    {
                                      $defaultValue = "'" . $defaultValue . "'";
                                    }
                                  }
                                  $co->setDefaultValue($defaultValue);
                                }

                                $co->setNullable($column["IS_NULLABLE"] === 'YES');
                                $co->setAutoIncrement(strpos($column["EXTRA"], "auto_increment") !== false);

                                $cols[] = $co;
                              }
                            }
                            else
                            {
                              $r = $columns;
                            }

                            if (!isset($r))
                            {
                              // primary key...
                              $primaryKeyColumns = $this->executeQuery("SELECT kcu.COLUMN_NAME AS COLUMN_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS tc INNER JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS kcu ON kcu.CONSTRAINT_CATALOG = tc.CONSTRAINT_CATALOG AND kcu.CONSTRAINT_SCHEMA = tc.CONSTRAINT_SCHEMA AND kcu.CONSTRAINT_NAME = tc.CONSTRAINT_NAME AND kcu.TABLE_CATALOG = tc.CONSTRAINT_CATALOG AND kcu.TABLE_SCHEMA = tc.TABLE_SCHEMA AND kcu.TABLE_NAME = tc.TABLE_NAME WHERE tc.CONSTRAINT_CATALOG = '" . $catalogName . "' AND tc.CONSTRAINT_SCHEMA = '" . $schemaName . "' AND tc.CONSTRAINT_NAME = 'PRIMARY' AND tc.TABLE_SCHEMA = '" . $schemaName . "' AND tc.TABLE_NAME = '" . $tableName . "' AND tc.CONSTRAINT_TYPE = 'PRIMARY KEY' ORDER BY kcu.ORDINAL_POSITION");
                              if (is_array($primaryKeyColumns))
                              {
                                $pkcLen = count($primaryKeyColumns);
                                if ($pkcLen > 0)
                                {
                                  $pk = new ExportedPrimaryKey();
                                  if (substr(strtolower($tableName), 0, 1) == substr($tableName, 0, 1))
                                  {
                                    $pk->setName("pk_" . $tableName);
                                  }
                                  else
                                  {
                                    $pk->setName("PK_" . $tableName);
                                  }
                                  $pkCols = new JSLArray();
                                  $pk->setColumns($pkCols);
                                  for ($pkcI = 0; $pkcI < $pkcLen; $pkcI++)
                                  {
                                    $primaryKeyColumn = $primaryKeyColumns[$pkcI];
                                    $pkCols[] = $primaryKeyColumn["COLUMN_NAME"];
                                  }
                                  $t->setPrimaryKey($pk);
                                }
                              }
                              else
                              {
                                $r = $primaryKeyColumns;
                              }
                            }

                            if (!isset($r))
                            {
                              // foreign keys...
                              $foreignKeys = $this->executeQuery("SELECT tc.CONSTRAINT_NAME AS CONSTRAINT_NAME, rc.UPDATE_RULE AS UPDATE_RULE, rc.DELETE_RULE AS DELETE_RULE, rc.UNIQUE_CONSTRAINT_SCHEMA AS REFERENCED_SCHEMA, rc.REFERENCED_TABLE_NAME AS REFERENCED_TABLE FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS tc INNER JOIN INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS AS rc ON rc.CONSTRAINT_CATALOG = tc.CONSTRAINT_CATALOG AND rc.CONSTRAINT_SCHEMA = tc.CONSTRAINT_SCHEMA AND rc.CONSTRAINT_NAME = tc.CONSTRAINT_NAME AND rc.TABLE_NAME = tc.TABLE_NAME WHERE tc.CONSTRAINT_CATALOG = '" . $catalogName . "' AND tc.CONSTRAINT_SCHEMA = '" . $schemaName . "' AND tc.TABLE_SCHEMA = '" . $schemaName . "' AND tc.TABLE_NAME = '" . $tableName . "' AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY' ORDER BY tc.CONSTRAINT_NAME");
                              if (is_array($foreignKeys))
                              {
                                $fkLen = count($foreignKeys);
                                $foreignKeyNames = array();
                                for ($fkI = 0; $fkI < $fkLen; $fkI++)
                                {
                                  $foreignKey = $foreignKeys[$fkI];
                                  $fk = new ExportedForeignKey();
                                  $constraintName = $foreignKey["CONSTRAINT_NAME"];
                                  $fk->setName($constraintName);

                                  $foreignKeyNames[] = $constraintName;

                                  $fk->setOnUpdateReferentialAction($foreignKey["UPDATE_RULE"]);
                                  $fk->setOnDeleteReferentialAction($foreignKey["DELETE_RULE"]);
                                  $fk->setReferencedSchema($foreignKey["REFERENCED_SCHEMA"]);
                                  $fk->setReferencedTable($foreignKey["REFERENCED_TABLE"]);

                                  $refs = new JSLArray();
                                  $fk->setReferences($refs);

                                  $references = $this->executeQuery("SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE CONSTRAINT_CATALOG = '" . $catalogName . "' AND CONSTRAINT_SCHEMA = '" . $schemaName . "' AND CONSTRAINT_NAME = '" . $constraintName . "'AND TABLE_CATALOG = '" . $catalogName . "' AND TABLE_SCHEMA = '" . $schemaName . "' AND TABLE_NAME = '" . $tableName . "' ORDER BY ORDINAL_POSITION");
                                  if (is_array($references))
                                  {
                                    $rLen = count($references);
                                    for ($rI = 0; $rI < $rLen; $rI++)
                                    {
                                      $reference = $references[$rI];
                                      $ref = new ExportedReference();
                                      $ref->setColumn($reference["COLUMN_NAME"]);
                                      $ref->setReferencedColumn($reference["REFERENCED_COLUMN_NAME"]);
                                      $refs[] = $ref;
                                    }
                                    $fks[] = $fk;
                                  }
                                  else
                                  {
                                    $r = $references;
                                    exit;
                                  }
                                }
                              }
                              else
                              {
                                $r = $foreignKeys;
                              }
                            }

                            if (!isset($r))
                            {
                              // indexes...
                              $indexes = $this->executeQuery("SELECT DISTINCT NON_UNIQUE, INDEX_NAME, INDEX_COMMENT FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_CATALOG = '" . $catalogName . "' AND TABLE_SCHEMA = '" . $schemaName . "' AND TABLE_NAME = '" . $tableName . "' AND INDEX_NAME <> 'PRIMARY' ORDER BY INDEX_NAME");
                              if (is_array($indexes))
                              {
                                $ixLen = count($indexes);
                                for ($ixI = 0; $ixI < $ixLen; $ixI++)
                                {
                                  $index = $indexes[$ixI];
                                  $indexName = $index["INDEX_NAME"];

                                  if (array_search($indexName, $foreignKeyNames) === false)
                                  {
                                    $ix = new ExportedIndex();
                                    $ix->setName($indexName);
                                    $ix->setComment($index["INDEX_COMMENT"]);
                                    $ix->setClustered(false);
                                    $ix->setUnique($index["NON_UNIQUE"] == 0);
                                    $ics = new JSLArray();
                                    $ix->setIndexedColumns($ics);

                                    $indexedColumns = $this->executeQuery("SELECT COLUMN_NAME, COLLATION, SUB_PART FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_CATALOG = '" . $catalogName . "' AND TABLE_SCHEMA = '" . $schemaName . "' AND TABLE_NAME = '" . $tableName . "' AND INDEX_NAME = '" . $indexName . "' ORDER BY SEQ_IN_INDEX");
                                    if (is_array($indexedColumns))
                                    {
                                      $icLen = count($indexedColumns);
                                      for ($icI = 0; $icI < $icLen; $icI++)
                                      {
                                        $indexedColumn = $indexedColumns[$icI];
                                        $ic = new ExportedIndexedColumn();
                                        $ic->setColumn($indexedColumn["COLUMN_NAME"]);
                                        $ic->setOrder(!isset($indexedColumn["COLLATION"]) || $indexedColumn["COLLATION"] == 'A' ? "ASC" : "DESC");
                                        $ic->setPrefixLength(isset($indexedColumn["SUB_PART"]) ? $indexedColumn["SUB_PART"] : null);
                                        $ics[] = $ic;
                                      }

                                      $ixs[] = $ix;
                                    }
                                    else
                                    {
                                      $r = $indexedColumns;
                                      exit;
                                    }
                                  }
                                }
                              }
                              else
                              {
                                $r = $indexes;
                              }
                            }
                          }
                        }
                      }
                    }
                    else
                    {
                      $r = $tables;
                    }
                  }
                }
              }
            }
            else
            {
              $r = $schemas;
            }
          }
        }
      }
      else
      {
        $r = $catalogs;
      }
      return isset($r) ? $r : $model;
    }

    public function executeQuery($sql)
    {
      $r = null;
      if ($this->oldMysqlInterfaceUsed)
      {
        $result = mysql_query($sql, $this->mysql);
        if ($result !== false)
        {
          $r = array();
          while ($row = mysql_fetch_assoc($result))
          {
            $r[] = $row;
          }
        }
        else
        {
          $r = mysql_error();
          if(isset($r))
          {
            $r = strval($r);
          }
        }
      }
      else
      {
        $result = $this->mysql->query($sql);
        if ($result !== false)
        {
          $r = array();
          while ($row = $result->fetch_assoc())
          {
            $r[] = $row;
          }
        }
        else
        {
          $r = $this->mysql->error;
          if(isset($r))
          {
            $r = strval($r);
          }
        }
      }
      return $r;
    }

    public function executeUpdate($sql)
    {
      $r = null;
      if ($this->oldMysqlInterfaceUsed)
      {
        $r = mysql_query($sql, $this->mysql);
        if ($r === false)
        {
          $r = mysql_error();
          if(isset($r))
          {
            $r = strval($r);
          }
        }
        else
        {
          $r = true;
        }
      }
      else
      {
        $r = $this->mysql->query($sql);
        if ($r === false)
        {
          $r = $this->mysql->error;
          if(isset($r))
          {
            $r = strval($r);
          }
        }
        else
        {
          $r = true;
        }
      }
      return $r;
    }
    
    public function getFullType($exportedColumn)
    {
      $r = null;
      $type = $exportedColumn->getType();
      $precision = $exportedColumn->getPrecision();
      $scale = $exportedColumn->getScale();
      $length = $exportedColumn->getLength();
      $pattern = null;
      switch($type)
      {
        case "decimal":
          $pattern = "decimal(precision,scale)";
          break;
        case "decimal unsigned":
          $pattern = "decimal(precision,scale) unsigned";
          break;
        case "numeric":
          $pattern = "numeric(precision,scale)";
          break;
        case "numeric unsigned":
          $pattern = "numeric(precision,scale) unsigned";
          break;
        case "bit":
          $pattern = "bit(precision)";
          break;
        case "datetime":
          if(!isset($precision) || $precision==0)
          {
            $pattern = "datetime";
          }
          else
          {
            $pattern = "datetime(precision)";
          }
          break;
        case "timestamp":
          if(!isset($precision) || $precision==0)
          {
            $pattern = "timestamp";
          }
          else
          {
            $pattern = "timestamp(precision)";
          }
          break;
        case "time":
          if(!isset($precision) || $precision==0)
          {
            $pattern = "time";
          }
          else
          {
            $pattern = "time(precision)";
          }
          break;
        case "year":
          if(!isset($precision) || $precision==4)
          {
            $pattern = "year";
          }
          else
          {
            $pattern = "year(precision)";
          }
          break;
        case "varchar":
          $pattern = "varchar(length)";
          break;
        case "char":
          $pattern = "char(length)";
          break;
        case "varbinary":
          $pattern = "varbinary(length)";
          break;
        case "binary":
          $pattern = "binary(length)";
          break;
      }
      if(isset($pattern))
      {
        $r = $pattern;
        $r = str_replace("length", strval($length), $r);
        $r = str_replace("precision", strval($precision), $r);
        $r = str_replace("scale", strval($scale), $r);
      }
      else
      {
        $r = $type;
      }
      return $r;
    }
    
    public function hasLength($type)
    {
      $r = false;
      switch ($type)
      {
        case 'varchar':
        case 'char':
          $r = true;
          break;
        default:
          $r = false;
      }
      return $r;
    }

    public function hasPrecision($type)
    {
      $r = false;
      switch ($type)
      {
        case 'decimal':
        case 'decimal unsigned':
        case 'dec':
        case 'dec unsigned':
        case 'fixed':
        case 'fixed unsigned':
        case 'numeric':
        case 'numeric unsigned':
        case 'datetime':
        case 'timestamp':
        case 'time':
          $r = true;
          break;
        default:
          $r = false;
      }
      return $r;
    }

    public function hasScale($type)
    {
      $r = false;
      switch ($type)
      {
        case 'decimal':
        case 'decimal unsigned':
        case 'dec':
        case 'dec unsigned':
        case 'fixed':
        case 'fixed unsigned':
        case 'numeric':
        case 'numeric unsigned':
          $r = true;
          break;
        default:
          $r = false;
      }
      return $r;
    }

    public function isDateTimeType($type)
    {
      $r = false;
      switch ($type)
      {
        case 'datetime':
        case 'timestamp':
          $r = true;
          break;
      }
      return $r;
    }

    public function isDateType($type)
    {
      $r = false;
      switch ($type)
      {
        case 'date':
          $r = true;
          break;
      }
      return $r;
    }

    public function isTimeType($type)
    {
      $r = false;
      switch ($type)
      {
        case 'time':
          $r = true;
          break;
      }
      return $r;
    }

    public function isStringType($type)
    {
      $r = false;
      switch ($type)
      {
        case 'varchar':
        case 'char':
        case 'text':
        case 'clob':
        case 'tinytext':
        case 'mediumtext':
        case 'longtext':
          $r = true;
          break;
        default:
          $r = false;
      }
      return $r;
    }

    public function isFixedPointNumericType($type)
    {
      $r = false;
      switch ($type)
      {
        case 'decimal':
        case 'decimal unsigned':
        case 'dec':
        case 'dec unsigned':
        case 'fixed':
        case 'fixed unsigned':
        case 'numeric':
        case 'numeric unsigned':
          $r = true;
          break;
        default:
          $r = false;
      }
      return $r;
    }

    public function isNumericType($type)
    {
      $r = false;
      switch ($type)
      {
        case 'tinyint':
        case 'tinyint unsigned':
        case 'smallint':
        case 'smallint unsigned':
        case 'mediumint':
        case 'mediumint unsigned':
        case 'int':
        case 'int unsigned':
        case 'integer':
        case 'integer unsigned':
        case 'bigint':
        case 'bigint unsigned':
        case 'decimal':
        case 'decimal unsigned':
        case 'dec':
        case 'dec unsigned':
        case 'fixed':
        case 'fixed unsigned':
        case 'numeric':
        case 'numeric unsigned':
        case 'float':
        case 'real':
        case 'double':
        case 'double precision':
          $r = true;
          break;
        default:
          $r = false;
      }
      return $r;
    }

    public function getNumericMinValueAsString($type, $precision, $scale)
    {
      $r = null;
      switch ($type)
      {
        case 'tinyint unsigned':
        case 'smallint unsigned':
        case 'mediumint unsigned':
        case 'int unsigned':
        case 'integer unsigned':
        case 'bigint unsigned':
          $r = "0";
          break;
        case 'tinyint':
          $r = "-128";
          break;
        case 'smallint':
          $r = "-32768";
          break;
        case 'mediumint':
          $r = "-8388608";
          break;
        case 'int':
          $r = "-2147483648";
          break;
        case 'integer':
          $r = "-2147483648";
          break;
        case 'bigint':
          $r = "-9223372036854775808";
          break;
        case 'decimal':
        case 'dec':
        case 'fixed':
        case 'numeric':
          $p = isset($precision) ? $precision : 10;
          $s = isset($scale) ? $scale : 0;
          $r = '-' . (str_repeat("9", $p - $s)) . ($scale > 0 ? '.' . (str_repeat('9', $s)) : '');
          break;
        case 'decimal unsigned':
        case 'dec unsigned':
        case 'fixed unsigned':
        case 'numeric unsigned':
          $r = "0";
          break;
        case 'float':
        case 'real':
        case 'double':
        case 'double precision':
          break;
      }
      return $r;
    }

    public function getNumericMaxValueAsString($type, $precision, $scale)
    {
      $r = null;
      switch ($type)
      {
        case 'tinyint':
          $r = "127";
          break;
        case 'tinyint unsigned':
          $r = "255";
          break;
        case 'smallint':
          $r = "32767";
          break;
        case 'smallint unsigned':
          $r = "65535";
          break;
        case 'mediumint':
          $r = "8388607";
          break;
        case 'mediumint unsigned':
          $r = "16777215";
          break;
        case 'int':
          $r = "2147483647";
          break;
        case 'int unsigned':
          $r = "4294967295";
          break;
        case 'integer':
          $r = "2147483647";
          break;
        case 'integer unsigned':
          $r = "4294967295";
          break;
        case 'bigint':
          $r = "9223372036854775807";
          break;
        case 'bigint unsigned':
          $r = "18446744073709551615";
          break;
        case 'decimal':
        case 'decimal unsigned':
        case 'dec':
        case 'dec unsigned':
        case 'fixed':
        case 'fixed unsigned':
        case 'numeric':
        case 'numeric unsigned':
          $p = isset($precision) ? $precision : 10;
          $s = isset($scale) ? $scale : 0;
          $r = (str_repeat("9", $p - $s)) . ($scale > 0 ? '.' . (str_repeat('9', $s)) : '');
          break;
        case 'float':
        case 'real':
        case 'double':
        case 'double precision':
          break;
      }
      return $r;
    }

    public function isIntegerType($type)
    {
      $r = false;
      switch ($type)
      {
        case 'tinyint':
        case 'tinyint unsigned':
        case 'smallint':
        case 'smallint unsigned':
        case 'mediumint':
        case 'mediumint unsigned':
        case 'int':
        case 'int unsigned':
        case 'integer':
        case 'integer unsigned':
        case 'bigint':
        case 'bigint unsigned':
          $r = true;
          break;
        default:
          $r = false;
      }
      return $r;
    }
  }
}
?>