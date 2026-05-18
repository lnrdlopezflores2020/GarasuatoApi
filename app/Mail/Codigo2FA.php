<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class Codigo2FA extends Mailable
{
    public $usuario;
    public $codigo;
    public $mensaje;

    public function __construct($usuario, $codigo)
    {
        $this->usuario = $usuario;

        $this->codigo = $codigo;

        $this->mensaje =
            'Tu código de acceso seguro para GARASUATO es:';
    }

    public function build()
    {
        return $this
            ->subject('Verificación GARASUATO')
            ->view('emails.codigo2fa');
    }
}