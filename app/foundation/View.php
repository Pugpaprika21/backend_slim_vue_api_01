<?php

namespace App\Foundation;

use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class View
{
    use ViewTrait;

    /**
     * @param string $path
     * @param array $viewData
     * @return string|void
     * @throws Exception
     */
    public function render(Response $response, string $path, array $viewData = [])
    {
        $viewPath = $this->rootApps['page.render'] . $path;

        if (!file_exists($viewPath)) {
            throw new Exception("View path not found: {$viewPath}");
        }

        $response->getBody()->write($this->process($viewPath, $viewData));
        return $response;
    }

    /**
     * @param string $path
     * @param array $viewData
     * @return string
     * @throws Exception
     */
    private function process(string $viewPath, array $viewData = []): string
    {
        extract($viewData);
        ob_start();

        try {
            require $viewPath;
        } catch (Exception $e) {
            ob_end_clean();
            throw new Exception("{$viewPath}");
        }

        return ob_get_clean();
    }

    /**
     * @param string $cssPath
     * @return string
     * @throws Exception
     */
    private function css(string $cssPath): string
    {
        return $this->fileLoadExit($this->rootApps['page.css'] . $cssPath);
    }

    /**
     * @param string $scriptPath
     * @return string
     * @throws Exception
     */
    private function script(string $scriptPath): string
    {
        return $this->fileLoadExit($this->rootApps['page.js'] . $scriptPath);
    }

    /**
     * @param string $layoutPath
     * @return string
     * @throws Exception
     */
    private function layout(string $layoutPath): string
    {
        return $this->fileLoadExit($this->rootApps['page.layout'] . $layoutPath);
    }

    /**
     * @param string $templatePath
     * @return string
     * @throws Exception
     */
    private function template(string $templatePath): string
    {
        return $this->fileLoadExit($this->rootApps['page.template'] . $templatePath);
    }

    /**
     * @param string $realPath
     * @return string|void
     */
    private function fileLoadExit(string $realPath)
    {
        if (file_exists($realPath)) {
            return file_get_contents($realPath);
        }
        throw new Exception("Not found: {$realPath}");
    }

    /**
     * @param string $imagePath
     * @return string
     * @throws Exception
     */
    private function img(string $imagePath)
    {
        $realPath = $this->rootApps['app.upload'] . $imagePath;

        if (file_exists($realPath)) {
            $allowedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'JPG'];
            $fileExtension = pathinfo($realPath, PATHINFO_EXTENSION);

            if (in_array($fileExtension, $allowedExtensions)) {
                $imageContents = file_get_contents($realPath);
                if (!$imageContents) {
                    throw new Exception("extensions not found. {$imagePath}");
                }

                $imageBase64 = base64_encode($imageContents);
                return "data:image/{$fileExtension};base64,{$imageBase64}";
            }
        }

        throw new Exception("extensions not found. {$imagePath}");
    }
}
