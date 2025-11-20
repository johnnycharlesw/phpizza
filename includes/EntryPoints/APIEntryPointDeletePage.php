<?php
namespace PHPizza;
class APIEntryPointDeletePage extends APIEntryPoint {
    public function run_delete_page(){
        // Handle POST request to delete an existing page
        global $homepageName, $dbServer, $dbUser, $dbPassword, $dbName, $dbType;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
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
        // Delete page
        $pagedb->deletePage($existingPage['id']);
        return [
            'status' => 200,
            'title' => $title,
            'html' => 'Page deleted successfully.',
            'description' => '',
            'keywords' => [],
        ];
    }
    public function run(){
        $this->run_begin();
        $data=$this->run_delete_page();
        $this->run_end($data);
    }
}