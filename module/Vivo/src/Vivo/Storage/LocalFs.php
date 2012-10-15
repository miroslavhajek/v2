<?php
namespace Vivo\Storage;

/**
 * Implementation of the virtual file system over local filesystem.
 */
class LocalFs implements StorageInterface {
	/**
	 * Root path.
	 * @var string $root
	 */
	private $root;

	/**
	 * @param string $root Root path.
	 */
	public function __construct($root) {
		//@todo: check if exists, writable
		$this->root = $root;
	}

	private function getAbsolutePath($path) {
		return $this->root.$path;
	}

	/**
	 * @todo prepsat telo na dirname($path) ???
	 *
	 * Enter description here ...
	 * @param unknown_type $path
	 * @return Ambigous <boolean, string>
	 */
	private function dirname($path) {
		return ($p = strrpos($path, '/')) ? substr($path, 0, $p) : false;
	}

	/**
	 * Creates dir recursive.
	 * @param string $dir
	 * @throws Vivo\Storage\IOException Cannot create directory.
	 */
	private function mkdir($path) {
		$absPath = $this->getAbsolutePath($path);
		clearstatcache(true);
		if (!is_dir($absPath)) {
			//echo "mkdir $abs_dir_path<br>";
			clearstatcache(true);
			if (!@mkdir($absPath, 0777, true) && !is_dir($absPath)) {
				$error = null;
				$lastError = error_get_last();
				if ($lastError && isset($lastError['message'])) {
					$error = $lastError['message'];
				}
				throw new Exception\IOException("Cannot create directory '{$absPath}' for '{$path}' ({$error})");
			}
		}
		clearstatcache(true);
		chmod($absPath, 0777);
	}

	/**
	 * Checks whether a file or directory exists.
	 *
	 * @param string $path Path to the file.
	 * @param bool
	 */
	public function contains($path) {
		$absPath = $this->getAbsolutePath($path);
		return file_exists($absPath);
	}

	/**
	 * @param string $path
	 * @return bool
	 */
	public function isObject($path) {
		return is_file($this->getAbsolutePath($path));
	}

	/**
	 * Gets file modification time if exists.
	 * @param string $path Path to the file.
	 * @return int|false
	 */
	public function mtime($path) {
		$absPath = $this->getAbsolutePath($path);
		return file_exists($absPath) ? filemtime($absPath) : false;
	}

	/**
	 * Reads entire file into a string.
	 * @param string $path
	 * @return string
	 */
	public function get($path) {
		$return = null;
		if ($this->contains($path)) {
			$absPath = $this->getAbsolutePath($path);
			$return = @file_get_contents($absPath);
		}
		return $return;
	}

	/**
	 * Write a string to a file.
	 *
	 * @param string $path
	 * @param mixed $variable
	 * @throws Vivo\Storage\IOException Cannot create directory.
	 */
	public function set($path, $variable) {
		$this->mkdir($this->dirname($path));
		$absPath = $this->getAbsolutePath($path);

		$result = file_put_contents($absPath, $variable);
		if ($result === false) {
			$error = null;
			$lastError = error_get_last();
			if ($lastError && isset($lastError['message'])) {
				$error = $lastError['message'];
			}

			throw new Exception\IOException("Cannot write data to '$absPath' for '$path' ($error)");
		}
// 		chmod($absPath, 0777); //@todo: tohle je tu proc ????? @see self::mkdir
	}

	/**
	 * Sets access and modification time of file.
	 * @param string $path The name of the file being touched.
	 */
	public function touch($path) {
		touch($this->getAbsolutePath($path));
	}

	/**
	 * Move.
	 * @param string $path Source path.
	 * @param string $target Destination path.
	 * @return bool
	 */
	public function move($path, $target) {
		if (stripos(getenv('OS'), 'windows') !== false) {
			// solves problems with php rename function on Windows (Access denied sometimes)
			if ($this->copy($path, $target)) {
				return (bool)$this->remove($path);
			} else {
				return false;
			}
		} else {
			$this->mkdir($target);
			return rename($this->getAbsolutePath($path), $this->getAbsolutePath($target));
		}
	}

	/**
	 * Copy.
	 * @param string $path Source path.
	 * @param string $target Destination path.
	 */
	public function copy($path, $target) {
		$count = 0;
		$this->mkdir($target);
		if (is_dir($this->getAbsolutePath($path))) {
			@mkdir($this->getAbsolutePath($target));
			foreach ($this->scan($path) as $name)
				$count +=  $this->copy("$path/$name", "$target/$name");
		} else {
			$count += copy($this->getAbsolutePath($path), $this->getAbsolutePath($target));
		}
		return $count;
	}

	/**
	 * Returns array with file paths.
	 * @param string $path
	 * @return array
	 */
	public function scan($path) {
		$names = array();
		$absPath = $this->getAbsolutePath($path);
		if ($dir = @scandir($absPath)) {
			foreach ($dir as $name) {
				if ($name{0} != '.') {
					$names[] = $name;
				}
			}
		}
		return $names;
	}

	/**
	 * Removes directory.
	 * @param string $path
	 * @return int Count of deleted directories.
	 */
	public function remove($path) {
		$count = 0;
		$absPath = $this->getAbsolutePath($path);
		if (is_dir($absPath)) {
			foreach ($this->scan($path) as $name)
				$count += $this->remove("$path/$name");
			rmdir($absPath);
			$count++;
		} else if (is_file($absPath) || is_link($absPath)) {
			unlink($absPath);
			$count++;
		}
		return $count;
	}

}
