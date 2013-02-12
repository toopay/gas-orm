<?php namespace Gas;

/**
 * CodeIgniter Gas ORM Packages
 *
 * A lighweight and easy-to-use ORM for CodeIgniter
 * 
 * This packages intend to use as semi-native ORM for CI, 
 * based on the ActiveRecord pattern. This ORM uses CI stan-
 * dard DB utility packages also validation class.
 *
 * @package     Gas ORM
 * @category    ORM
 * @version     2.1.2
 * @author      Taufan Aditya A.K.A Toopay
 * @link        http://gasorm-doc.taufanaditya.com/
 * @license     BSD
 *
 * =================================================================================================
 * =================================================================================================
 * Copyright 2011 Taufan Aditya a.k.a toopay. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are
 * permitted provided that the following conditions are met:
 * 
 * 1. Redistributions of source code must retain the above copyright notice, this list of
 * conditions and the following disclaimer.
 * 
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list
 * of conditions and the following disclaimer in the documentation and/or other materials
 * provided with the distribution.
 * 
 * THIS SOFTWARE IS PROVIDED BY Taufan Aditya a.k.a toopay ‘’AS IS’’ AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL Taufan Aditya a.k.a toopay OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * The views and conclusions contained in the software and documentation are those of the
 * authors and should not be interpreted as representing official policies, either expressed
 * or implied, of Taufan Aditya a.k.a toopay.
 * =================================================================================================
 * =================================================================================================
 */

/**
 * Gas\Data Class.
 *
 * @package     Gas ORM
 * @since       2.0.0
 */

class Data implements \ArrayAccess, \Iterator, \Countable {

    /**
     * @var array Data collection
     */
    private $collections = array();

    /**
     * Constructor
     */
    public function __construct($collections = array())
    {
        $this->collections = $collections;
    }

    /**
     * Handle array isset
     *
     * @return  bool
     */
    public function offsetExists($offset) 
    {
        return isset($this->collections[$offset]);
    }

    /**
     * Handle array getter
     *
     * @return  mixed
     */
    public function offsetGet($offset) 
    {
        return isset($this->collections[$offset]) ? $this->collections[$offset] : FALSE;
    }

    /**
     * Handle array setter
     *
     * @return  void
     */
    public function offsetSet($offset, $value) 
    {
        if (is_null($offset)) 
        {
            $this->collections[] = $value;
        } 
        else 
        {
            $this->collections[$offset] = $value;
        }
    }
    
    /**
     * Handle array unsetter
     *
     * @return  void
     */
    public function offsetUnset($offset) 
    {
        unset($this->collections[$offset]);
    }

     /**
     * Handle rewind
     *
     * @return  void
     */
    public function rewind() 
    {
        reset($this->collections);
    }

    /**
     * Handle current
     *
     * @return  void
     */
    public function current() 
    {
        return current($this->collections);
    }

    /**
     * Handle key
     *
     * @return  mixed
     */
    public function key() 
    {
        return key($this->collections);
    }

    /**
     * Handle next
     *
     * @return  mixed
     */
    public function next() 
    {
        return next($this->collections);
    }

    /**
     * Handle last
     *
     * @return  mixed
     */
    public function last() 
    {
        end($this->collections) and $last_index = key($this->collections);
        $collections = $this->collections[$last_index];
        reset($this->collections);

        return $collections;
    }

    /**
     * Handle valid
     *
     * @return  bool
     */
    public function valid() 
    {
        return $this->current() !== FALSE;
    }    

    /**
     * Handle counter
     *
     * @return  int
     */
    public function count() 
    {
        return count($this->collections);
    }

    /**
     * Sorting collection Ascending
     *
     * @return  void
     */
    public function ksortAsc() 
    {
        ksort($this->collections);
    }

    /**
     * Sorting collection Descending
     *
     * @return  void
     */
    public function ksortDesc() 
    {
        krsort($this->collections);
    }

    /**
     * Collection getter
     *
     * @param   string
     * @param   mixed
     * @return  int
     */
    public function get($path = NULL, $default = FALSE) 
    {
        // Create new array for processing
        $array = $this->collections;

        if (is_null($path)) 
        {
            return $array;
        }

        // Remove outer dots, wildcards, or spaces and split the keys
        $path = trim($path, '.* ');
        $keys = explode('.', $path);

        do 
        {
            $key = array_shift($keys);

            if (ctype_digit($key)) 
            {
                // Make the key an integer
                $key = (int) $key;
            }

            if (isset($array[$key])) 
            {
                if ($keys) 
                {
                    if (is_array($array[$key])) 
                    {
                        // Dig down into the next part of the path
                        $array = $array[$key];
                    }
                    else
                    {
                        // Unable to dig deeper
                        break;
                    }
                } 
                else 
                {
                    // Found the path requested
                    return $array[$key];
                }
            } 
            elseif ($key === '*') 
            {
                // Handle wildcards
                if (empty($keys)) 
                {
                    return $array;
                }

                $values = array();

                foreach ($array as $arr) 
                {
                    if ($value = self::get($arr, implode('.', $keys))) 
                    {
                        $values[] = $value;
                    }
                }

                if ($values) 
                {
                    // Found the values requested
                    return $values;
                } 
                else 
                {
                    // Unable to dig deeper
                    break;
                }
            } 
            else 
            {
                // Unable to dig deeper
                break;
            }
        } while ($keys);

        // Unable to find the value requested
        return $default;
    }

    /**
     * Set/assign a collection data
     *
     * @param   mixed   Collection key
     * @param   mixed   Collection values
     * @return  void
     */
    public function set($key, $value) 
    {
        // If the key are arrays build associative array, otherwise build one level array
        if ($key = explode('.', $key)) 
        {
            switch(count($key)) 
            {
                case 1:
                    $this->collections[$key[0]] = $value;

                    break;

                case 2:
                    $this->collections[$key[0]][$key[1]] = $value;

                    break;

                case 3:
                    $this->collections[$key[0]][$key[1]][$key[2]] = $value;

                    break;

                case 4:
                    $this->collections[$key[0]][$key[1]][$key[2]][$key[3]] = $value;

                    break;
            }
        } 
        else 
        {
            $this->collections[$key] = $value;
        }
    }
}