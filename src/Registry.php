<?php

namespace MaiVu\Php;

use ArrayAccess;

class Registry implements ArrayAccess
{
	/** @var array */
	protected $data = [];

	public function __construct($data = null)
	{
		$this->data = $this->parse($data);
	}

	/**
	 * @param $data
	 *
	 * @return array
	 */
	public function parse($data)
	{
		return static::parseData($data);
	}

	public static function parseData($data)
	{
		if ($data instanceof Registry)
		{
			$data = $data->toArray();
		}
		elseif (is_object($data))
		{
			$data = (array) $data;
		}
		elseif (is_string($data))
		{
			if (strpos($data, '{') === 0 || strpos($data, '[') === 0)
			{
				$data = json_decode($data, true) ?: [];
			}
			elseif (is_file($data))
			{
				preg_match('/\.([a-z]+)$/i', $data, $matches);

				if (!empty($matches[1]))
				{
					switch ($matches[1])
					{
						case 'php':
							$data = include $data;
							break;

						case 'json':
							$data = json_decode(file_get_contents($data), true) ?: [];
							break;

						case 'ini':
							$data = parse_ini_file($data);
							break;
					}
				}
			}
		}

		if (empty($data))
		{
			return [];
		}

		return is_array($data) ? $data : [$data];
	}

	public function toArray()
	{
		return $this->data;
	}

	public static function request()
	{
		static $request = null;

		if (null === $request)
		{
			$request = new Registry(
				[
					'get'     => &$_GET,
					'post'    => &$_POST,
					'request' => &$_REQUEST,
					'server'  => &$_SERVER,
					'files'   => &$_FILES,
				]
			);
		}

		return $request;
	}

	public function merge($data)
	{
		$this->data = array_merge($this->data, $this->parse($data));

		return $this;
	}

	public function __toString()
	{
		return $this->toString();
	}

	public function toString()
	{
		return json_encode($this->data);
	}

	/**
	 * @inheritDoc
	 */
	public function offsetExists($offset)
	{
		return $this->has($offset);
	}

	public function has($path)
	{
		if (false === strpos($path, '.'))
		{
			return array_key_exists($path, $this->data);
		}

		$keys = explode('.', $path);
		$data = $this->data;

		foreach ($keys as $key)
		{
			if (!array_key_exists($key, $data))
			{
				return false;
			}

			$data = $data[$key];
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetGet($offset)
	{
		return $this->get($offset);
	}

	public function get($path, $defaultValue = null, $filter = null)
	{
		if (false === strpos($path, '.'))
		{
			$data = array_key_exists($path, $this->data) ? $this->data[$path] : $defaultValue;
		}
		else
		{
			$keys = explode('.', $path);
			$data = $this->data;

			foreach ($keys as $key)
			{
				if (!isset($data[$key]))
				{
					return $defaultValue;
				}

				$data = $data[$key];
			}
		}

		if ($filter)
		{
			$data = Filter::clean($data, $filter);
		}

		return $data;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetSet($offset, $value)
	{
		return $this->set($offset, $value);
	}

	public function set($path, $value, $filter = null)
	{
		if ($filter)
		{
			$value = Filter::clean($value, $filter);
		}

		if (false === strpos($path, '.'))
		{
			$this->data[$path] = $value;
		}
		else
		{
			$keys = explode('.', $path);
			$data = &$this->data;

			foreach ($keys as $key)
			{
				if (!isset($data[$key]))
				{
					$data[$key] = [];
				}

				$data = &$data[$key];
			}

			$data = $value;
		}

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function offsetUnset($offset)
	{
		if (false !== strpos($offset, '.'))
		{
			$offsets = explode('.', $offset);
			$data    = &$this->data;
			$endKey  = array_pop($offsets);

			foreach ($offsets as $offset)
			{
				if (!isset($data[$offset]))
				{
					return $this;
				}

				$data = &$data[$offset];
			}

			unset($data[$endKey]);
		}
		else
		{
			unset($this->data[$offset]);
		}

		return $this;
	}

	public function __get($name)
	{
		$data = $this->get($name);

		if (is_array($data))
		{
			return new Registry($data);
		}

		return $data;
	}
}