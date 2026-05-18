<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Código GARASUATO</title>
</head>

<body style="margin:0; padding:0; background:#f4f7f8; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">

<tr>
<td align="center" style="padding:40px 20px;">

<table width="600" cellpadding="0" cellspacing="0"
style="
background:#ffffff;
border-radius:25px;
overflow:hidden;
box-shadow:0 8px 20px rgba(0,0,0,0.08);
">

<tr>
<td
style="
background:#cae2e6;
padding:35px;
text-align:center;
"
>

<img
src="https://via.placeholder.com/120"
width="110"
>

<h1
style="
margin-top:15px;
color:#192718;
font-family:'Playfair Display', serif;
"
>
GARASUATO
</h1>

</td>
</tr>

<tr>
<td style="padding:40px; text-align:center;">

<h2 style="color:#192718;">
Hola, {{ $usuario }}
</h2>

<p style="color:#555; line-height:1.7;">
{{ $mensaje }}
</p>

<div style="margin:35px 0;">

<span
style="
display:inline-block;
background:#cae2e6;
padding:18px 35px;
border-radius:18px;
font-size:34px;
letter-spacing:10px;
font-weight:bold;
color:#192718;
"
>
{{ $codigo }}
</span>

</div>

<p style="color:#777;">
Este código expirará en unos minutos.
</p>

<p style="color:#999; font-size:14px;">
Si no solicitaste este acceso,
puedes ignorar este mensaje.
</p>

</td>
</tr>

<tr>
<td
style="
background:#f4f7f8;
padding:20px;
text-align:center;
font-size:13px;
color:#888;
"
>
© {{ date('Y') }} GARASUATO
</td>
</tr>

</table>

</td>
</tr>

</table>

</body>
</html>