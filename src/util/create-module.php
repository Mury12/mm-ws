<?php
require_once 'core/application/functions.php';
require_once 'core/application/autoload.php';
$argv;
if ($argc === 3) {
    try {
        // File name
        $name = preg_replace('/[ _]/im', '-', $argv[1]);
        // Folder name (domain)
        $folder = $argv[2];
        // Modules path
        $path =  'src/_ws/v2/';
        // Checks if this is a directory and if not create one
        if (!is_dir($path . $folder)) {
            mkdir($path . $folder);
        }
        // Check if file exists and asks for overwriting
        if (file_exists($path . $folder . '/' . $name . '.php')) {
            $done = false;
            while (!$done) {
                $choice = "n";
                echo "\nFile $folder/$name already exists. Do you want to overwrite? (y/n): ";
                fscanf(STDIN, "%c", $choice);
                if ('n' === strtolower($choice)) {
                    die("Process terminated by user.\n");
                } elseif ('y' === strtolower($choice)) $done = true;
            }
        }
        $className = "ExampleController";
        echo "\nPlease type the default controller name to use in this module or leave blank for ExampleController: ";
        fscanf(STDIN, "%s", $className);

        // Loads the template file
        $template = file_get_contents('src/util/templates/classes/Module.template');
        // Replaces the keywords
        $output = str_replace('{ENDPOINT_NAME}', ucfirst(preg_replace('/[\-]/im', ' ', $name)), $template);
        $output = str_replace('{CONTROLLER_NAME}', $className, $output);
        // Writes the file
        file_put_contents($path . $folder . '/' . $name . '.php', $output);
        print_r("\nModule successfully created at $path$folder/$name.php ");

        $done = false;
        // Asks if the user wants to create a service route for this module
        while (!$done) {
            print_r("\nDo you wish to create a service route for $name? (y/n): ");
            $choice = "n";
            fscanf(STDIN, '%c', $choice);
            if ('n' === strtolower($choice)) {
                die("\nThank you for using me. We are done for now.\n");
            } else {
                $done = true;
            }
        }


        // Explodes the URI to get the keywords to the path

        // Loads the current service routes array


        // Asks for the url to create the service route
        // Do not do this in your coding lmao
        pathname:
        $done = false;
        while (!$done) {
            print_r("\nOk. Type the URI in router folder (ws/v2 ws/v1, ws/v2/user, etc..): ");
            $str = "";
            fscanf(STDIN, '%s', $str);
            $str = trim(strtolower(preg_replace('/[ _]/im', '-', $str)));
            if (strlen($str)) $done = true;
        }
        // Checks if the selected router exists
        $uriParts = explode('/', $str);
        $routerFileName = $uriParts;
        array_pop($routerFileName);
        $routerFileName = implode('/', $routerFileName);
        if (!file_exists("src/routers/$routerFileName.php")) {
            print_r("\n The suggested router does not exist. Creating a new reference..");
            $newRouterTemplate = file_get_contents('src/util/templates/router.template');
            $suggestedDirectory = (explode('/', $routerFileName));
            array_pop($suggestedDirectory);
            $currentDirectory = "src/routers";
            foreach ($suggestedDirectory as $directory) {
                $currentDirectory .= "/$directory";
                if (!is_dir($currentDirectory)) {
                    mkdir($currentDirectory);
                }
            }
            file_put_contents("src/routers/$routerFileName.php", $newRouterTemplate);
        }

        $routes = require_once "src/routers/$routerFileName.php";
        // Loads the template
        // $template = file_get_contents('src/util/templates/service.template');
        $template = file_get_contents("src/routers/$routerFileName.php");

        $current = $routes;

        // $uri = explode('/', $str);
        $uri = $uriParts[sizeof($uriParts) - 1];

        if (array_key_exists($uri, $current)) {
            print_r("\nSorry, the path chosen is already taken. Please choose another.");
            goto pathname;
        }

        // Text to be added to the end of the array
        $text = "\t'$uri' => [\n";
        $text .= "\t\t'params' => ['id'],\n";
        $text .= "\t\t'body' => EndpointFactory::create()\n";
        $text .= "\t\t\t->post('$folder/$name', 'create')\n";
        $text .= "\t\t\t->get('$folder/$name', 'get')\n";
        $text .= "\t\t\t->put('$folder/$name', 'update')\n";
        $text .= "\t\t\t->delete('$folder/$name', 'delete'),\n";
        $text .= "\t\t// Add children routes calling the http methods from endpoint\n";
        $text .= "\t\t'another-children-route' => [\n";
        $text .= "\t\t\t'body' => EndpointFactory::create()\n";
        $text .= "\t\t\t\t->get('$folder/$name', 'exampleMethod'),\n\t\t]\n";
        $text .= "\t],\n];\n";


        // The route nesting is working but I didn't really found a way to save
        // the content as a legible text :/ sorry
        // foreach ($uri as $path) {
        //     if (array_key_exists($uri, $current)) {
        //         $current = &$current[$uri];
        //         $last++;
        //     } else {
        //         $current[$path] = [];
        //         $current = &$current[$path];
        //         $last = $path;
        //     };
        //     array_unshift($uri);
        // }
        // // Stores if the last keyword of the path was already a route
        // $hasRoute = false;
        // // Gets the lenght of the uri
        // if (array_key_exists('body', $current)) {
        //     $current[$name] = [];
        //     $current = &$current;
        //     $hasRoute = false;
        // }
        // $current = [
        //     'params' => ['param1', 'param2'],
        //     'body' => EndpointFactory::create()
        //         ->post($path . $folder . '/' . $name, 'exampleMethod')
        //         ->get($path . $folder . '/' . $name, 'exampleMethod')
        // ];
        // Replaces the keyword with the new array of routes
        $output = str_replace("];\n", $text, $template);
        // Saves the file
        file_put_contents("src/routers/$routerFileName.php", $output);
        print_r("\nService route for '$name' identified by '$routerFileName/$uri' successfully created at 'src/routers/$routerFileName.php'");
        print_r("\nThank you for using me. If you want to do it again, just call me `php create-module.php`.\n");
    } catch (Exception $e) {
        rmdir($path . $folder);
        throw $e;
    }
} else throw new Error("\nExpected two parameters `moduleName` and `folderName` but found " . ($argc - 1), 500);
