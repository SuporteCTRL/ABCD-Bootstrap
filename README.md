ABCD-Bootstrap
==============

ABCD Library redesenhado com Bootstrap.

>>ABCD

O ABCD (Administração de Bibliotecas e Centros de Documentação) foi desenvolvido pela BIREME em 2008. Este software foi criado para substituir o WinISIS, ainda utilizado em diversos locais.

O ABCD é uma solução web escrito em PHP que utiliza as tradicionais bases de dados CDS/ISIS desenvolvida pela UNESCO no fim dos anos de 1970.

O ABCD está na versão 1.3 Transicional, não é uma versão oficial para distribuição, mas muitas operações já são possíveis.


>> Bootstrap

Bootstrap é um framework de desenvolvimento de layouts responsivos criado pela equipe do Twitter. 

O Bootstrap é altamente adaptável para qualquer tipo de navegador e plataforma.

>> Objetivos

1. Redesenhar o ABCD utilizando o Bootstrap propondo melhorias no uso do programa.
2. Organizar e diminuir a quantidade de classes CSS para facilitar a customização da interface.
3. 3. Separar HTML do PHP
---> EXEMPLO
      

Original

      <?php
        foreach(){
          echo "<a href=\'#\'>". $var." </a>";
        }
      ?>



Como deve ficar

        <?php
          foreach(){
        ?>
          <a href="#"><?php echo  $var ?> </a>
        <?php
        }
        ?>


