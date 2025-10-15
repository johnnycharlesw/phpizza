<?php
namespace PHPizza;
class APIEntryPointUpdatePage extends APIEntryPoint{
    public function run_update_page(){
        // Handle POST request to update an existing page
        global $homepageName, $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $content = isset($_POST['content']) ? trim($_POST['content']) : '';
        $is_editor=true; // This endpoint is called by the editor
        if ($title === '') {
            return [
                'status' => 400,
                'title' => '',
                'html' => 'Title is required.',
                'description' => '',
                'keywords' => [],
            ];
        }
        if ($content === '') {
            return [
                'status' => 400,
                'title' => '',
                'html' => 'Content is required.',
                'description' => '',
                'keywords' => [],
            ];
        }
        $pagedb=new PageDatabase(
            $dbServer,
            $dbUser,
            $dbPassword,
            $dbName,
            $dbType
        );
        // Check if page exists
        $existingPage = $pagedb->getPage($title);
        if (!$existingPage) {
            return [
                'status' => 404,
                'title' => '',
                'html' => 'Page not found.',
                'description' => '',
                'keywords' => [],
            ];
        }
        // Update page
        $pagedb->updatePage($existingPage['id'], $title, $content);
        return [
            'status' => 200,
            'title' => $title,
            'html' => 'Page updated successfully.',
            'description' => '',
            'keywords' => [],
        ];
    }
    public function run(){
        $this->run_begin();
        $data=$this->run_update_page();
        $this->run_end($data);
    }
}