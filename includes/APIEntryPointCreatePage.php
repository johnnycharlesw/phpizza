<?php
namespace PHPizza;
class APIEntryPointCreatePage extends APIEntryPoint{
    public function run(){
        $this->run_begin();
        $data=$this->run_create_page();
        $this->run_end($data);
    }

    public function run_create_page(){
        // Handle POST request to create a new page
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

        // Check if page already exists
        $existingPage = $pagedb->getPage($title);
        if ($existingPage) {
            return [
                'status' => 409,
                'title' => '',
                'html' => 'A page with this title already exists.',
                'description' => '',
                'keywords' => [],
            ];
        }
        // Create the new page
        $success = $pagedb->createPage($title, $content);
        if ($success) {
            return [
                'status' => 201,
                'title' => $title,
                'html' => 'Page created successfully.',
                'description' => '',
                'keywords' => [],
            ];
        } else {
            return [
                'status' => 500,
                'title' => '',
                'html' => 'Failed to create page due to a server error.',
                'description' => '',
                'keywords' => [],
            ];
        }
    }
}