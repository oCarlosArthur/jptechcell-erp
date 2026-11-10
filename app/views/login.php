<!DOCTYPE html>
<html lang="pt-BR">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login - JP TechCell ERP</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{

    background:#111827;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;

}

.login{

    width:420px;
    background:#1f2937;
    padding:40px;
    border-radius:15px;
    box-shadow:0 0 20px rgba(0,0,0,.5);

}

h1{

    color:#22c55e;
    text-align:center;
    margin-bottom:30px;

}

input{

    width:100%;
    padding:14px;
    margin-bottom:15px;
    border:none;
    border-radius:8px;
    font-size:15px;

}

button{

    width:100%;
    padding:15px;
    border:none;
    border-radius:8px;
    background:#22c55e;
    color:white;
    font-size:16px;
    cursor:pointer;

}

button:hover{

    background:#16a34a;

}

</style>

</head>

<body>

<div class="login">

<h1>JP TechCell ERP</h1>

<form action="index.php?rota=autenticar" method="POST">

<input
type="text"
name="usuario"
placeholder="Usuário"
required>

<input
type="password"
name="senha"
placeholder="Senha"
required>

<button>

Entrar

</button>

</form>

</div>

</body>
</html>