<?php

namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface;
use Slim\Flash\Messages;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class Controller
{
    public function __construct(
        protected readonly Messages $flash,
        private readonly Twig $twig,
    ) {
    }

    protected function render(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        try {
            return $this->twig->render($response, $template, $data);
        } catch (LoaderError $e) {
            die($e->getMessage());
        } catch (SyntaxError $e) {
            die($e->getMessage());
        } catch (RuntimeError $e){
            die($e->getMessage());
        }
    }

//  add potential redirect method to not reuse $response->withHeader million times
}