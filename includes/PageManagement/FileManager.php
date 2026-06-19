<?php
namespace PHPizza\PageManagement;
use PHPizza\Warning;
class FileManager
{
    public string $folder;

    public function __construct($folder = __DIR__) {
        $this->folder = $folder;
    }

    private function resolve_relative_path(string $file) {
        return $this->folder . '/' . $file;
    }

    public function get_file_exists(string $file): bool {
        $filepath = $this->resolve_relative_path($file);
        return file_exists($filepath);
    }

    public function is_writable(string $file): bool {
        $filepath = $this->resolve_relative_path($file);
        return is_writable($filepath);
    }

    public function read_file(string $file): string {
        if ($this->get_file_exists($file)) {
            return file_get_contents($this->resolve_relative_path($file));
        } else {
            throw new Warning("File not found: $file");
            return "";
        }
    }

    public function write_file(string $file, $data) {
        if ($this->get_file_exists($file)) {
            file_put_contents($this->resolve_relative_path($file), $data);
        } else {
            throw new Warning("File not found: $file");
        }
    }

    public function deserialize(string $file): object {
        $contents_json = $this->read_file($file);
        $contents = json_decode($contents_json, false);
        return $contents;
    }

    public function serialize(string $file, object $data): bool {
        try {
            $json = json_encode($data, JSON_FORCE_OBJECT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
            $this->write_file($file, $json);
            return true;
        } catch (JsonException $th) {
            throw new Warning("Object not serialized: JSON parser failed");
            return false;
        }
    }

    public function touch_file(string $file) {
        touch($this->resolve_relative_path($file));
    }

    public function delete_file(string $file) {
        if ($this->get_file_exists($file)) {
            unlink($this->resolve_relative_path($file));
        } else {
            throw new Warning("File \"$file\" not deleted because it does not exist");
        }
    }

    public function symlink_file(string $file, string $symlink) {
        if ($this->get_file_exists($file)) {
            if ($this->get_file_exists($symlink)) {
                throw new Warning("Cannot symlink $symlink to $file because $symlink already exists as a file");
            } else {
                $target = $this->resolve_relative_path($file);
                $link = $this->resolve_relative_path($link);
                symlink($target, $link);
            }
        } else {
            throw new Warning("Cannot symlink to file $file because it does not exist");
            
        }
    }
}
