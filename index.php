<!DOCTYPE html>
<html>
<head>
    <title>
        <?php $config = include('config.php');printf($config["server-name"] . " - Comprar VIP");?>
    </title>
    <link rel="stylesheet" type="text/css" href="bootstrap.min.css">
    <script type="text/javascript" src="jquery.js"></script>
    <script type="text/javascript" src="jquery.mask.min.js"></script>
    <script type="text/javascript" src="bootstrap.min.js"></script>
    <script type="text/javascript" src="eModal.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $.ajaxSetup({ cache: false });

            $.extend(
            {
                redirectPost: function(location, args)
                {
                    var form = '';
                    $.each( args, function( key, value ) {
                        form += '<input type="hidden" name="'+key+'" value="'+value+'">';
                    });
                    $('<form action="'+location+'" method="POST">'+form+'</form>').appendTo('body').submit();
                }
            });

            $("#nick").mask("xxxxxxxxxxxxxxxx",{translation:{'x':{pattern: /[A-Za-z0-9_]/}}});
            $("#senha").mask("xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",{translation:{'x':{pattern: /[^ ]+/}}});

            $("#info").click(function() {
                var codigo = $("#produto").val();
                var titulo = $("#produto option:selected").text() + " - Detalhes";

                var url = window.location.pathname + "detalhes/" + codigo + ".html";
                eModal.ajax(url, titulo);

            });

            $("#formulario").submit(function( event ) {
                event.preventDefault();
                var nick = $("#nick").val();
                var senha = $("#senha").val();
                $.ajax({
                    method: "POST",
                    url: "accountCheck.php",
                    data: { player: nick, password: senha }
                })
                .done(function( msg ) {
                    if (msg == "true") {
                        nick = window.btoa(nick);
                        var codigo = $("#produto").val();
                        $.redirectPost('comprar.php', {player: nick, produto: codigo});
                    } else {
                        eModal.alert('Nick ou senha incorretos! Verifique e tente novamente.', 'Atenção!');
                    }
                });
            });
            
        });
    </script>
    <style type="text/css">
        #con {
            margin: 0 auto;
            margin-top: 10%;
            width: 400px;
            border: 1px solid black;
            border-radius: 7px;
        }
        #title {
            margin-left: 25%;
        }
        #buy {
            margin-left: 25%;
        }
        #termos {
            margin-left: 25%;
        }
        #info {
            margin-left: 37.5%;
        }
        .form-control {
            width: 300px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div id="con">
    <h1 id="title">Comprar VIP</h1><br>
    <form id="formulario" action="">
        <input id="nick" class="form-control" type="text" name="nick" required placeholder="Nick no jogo" autocomplete="off"><br><br>
        <input id="senha" class="form-control" type="password" name="" required placeholder="Senha"><br><br>
        <select class="form-control" id="produto">
            <?php
                $config = include('config.php');
                $con = mysqli_connect($config["host"], $config["user"], $config["pass"], $config["db"]) 
                or die(mysqli_connect_error());
                $query = mysqli_query($con, "SELECT * FROM `produtos`") or die (mysqli_error($con));
                if (mysqli_num_rows($query) == 0) {
                    echo "<script>
                    eModal.alert('Houve um problema ou estamos em manutenção!', 'Volte mais tarde!');
                    setTimeout(function(){
                        window.location = 'http://google.com.br';
                    }, 5000);
                    </script>";
                    return;
                }
                while ($produto = $query->fetch_array(MYSQLI_ASSOC)) {
                    $codigo = $produto["codigo"];
                    $nome = $produto["nome"];
                    $preco = number_format($produto["preco"], 2, ',', '');
                    echo "<option value='" . $codigo . "'>" . $nome . " - R$" . $preco ."</option>";
                }
                mysqli_close($con);
            ?>
        </select>
        <a id="info" href="#">Ver detalhes</a>
        <br><br>
        <input type="checkbox" id="termos" required> Concordo com os <a href="termos.php" target="_blank">Termos</a></input>
        <br><br>
        <input id="buy" class="btn btn-primary" type="submit" value="Comprar com PagSeguro"><br><br>
    </form>
</div>
</body>
</html>