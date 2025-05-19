<?php

var_dump($data);
$produtos = $data['produtos'];

?>
<center>
<div class="produtos">
<?php foreach ($produtos as $produto) { ?>
    <div class="produto" style="background-color: #EAEAEA; width: 20%">
        <p>Nome: <?php echo $produto['nome'] ?></p>
        <p>Preço: <?php echo $produto['preco'] ?></p>
        <p>Quantidade: <?php echo $produto['quantity'] ?></p>
    </div>
<?php } ?>
</div>