# dbs-auto-shop
Sistema automático de vendas para servidores de Minecraft usando PagSeguro

Esse sistema é uma combinação de site + plugin (Bukkit) para automatizar a venda de VIPS.

Requisitos:
- Hospedagem com PHP e MySQL (preferivel sem limite de tráfego)
- Servidor de Minecraft rodando Bukkit ou Spigot
- Plugin AuthMe instalado e interligado ao MySQL (para autenticar os usuários)
- Conta PagSeguro (para o encaminhamento das vendas)

Instalação:
- Baixe os arquivos como ZIP
- Extraia-os em uma pasta de sua hospedagem, ex: /vip
- Abra o arquivo "config.php" e configure o sistema
- Baixe o [plugin](https://github.com/zDubsCrazy/dbs-auto-shop-plugin) e coloque-o no servidor
- Configure o plugin com os mesmos dados
- Reinicie o servidor
