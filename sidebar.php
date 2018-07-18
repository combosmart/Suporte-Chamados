<aside id="sidebar-left" class="sidebar-left">

    <div class="sidebar-header">
        <div class="sidebar-title">
            Navegação
        </div>
        <div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
            <i class="fa fa-bars" aria-label="Toggle sidebar"></i>
        </div>
    </div>

    <div class="nano">
        <div class="nano-content">
            <nav id="menu" class="nav-main" role="navigation">
            
                <ul class="nav nav-main">
                    <li>
                        <a class="nav-link" href="../main/dashboard.php">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            <span>Início</span>
                        </a>                        
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="fa fa-columns" aria-hidden="true"></i>
                            <span>Cadastro</span>
                        </a>
                        <ul class="nav nav-children">
                            <!--
                            <li>
                                <a class="nav-link" href="index.html">
                                    Usuários
                                </a>
                            </li>
                            -->                            
                            <li>
                                <a class="nav-link" href="../clients/clients.php">
                                    Clientes
                                </a>
                            </li>                                                        
                            <li>
                                <a class="nav-link" href="../players/players.php">
                                    Pontos
                                </a>
                            </li>                            
                            <li>
                                <a class="nav-link" href="../machines/machines.php">
                                    Equipamentos
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="fa fa-columns" aria-hidden="true"></i>
                            <span>Quilometragem</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="../routes/routes.php">
                                    Registro
                                </a>
                            </li>                            
                        </ul>
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="fa fa-columns" aria-hidden="true"></i>
                            <span>Chamados</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="../tickets/tickets.add.php">
                                    Novo
                                </a>
                            </li>                            
                            <li>
                                <a class="nav-link" href="../tickets/tickets.php">
                                    Pesquisar
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-parent">
                        <a class="nav-link" href="#">
                            <i class="fa fa-columns" aria-hidden="true"></i>
                            <span>Atendimentos</span>
                        </a>
                        <ul class="nav nav-children">
                            <li>
                                <a class="nav-link" href="../atdmt/atdmt.add.php">
                                    Novo
                                </a>
                            </li>                            
                            <li>
                                <a class="nav-link" href="../atdmt/atdmt.php">
                                    Pesquisar
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a class="nav-link" href="../logout.php">
                            <i class="fa fa-external-link" aria-hidden="true"></i>
                            <span>Sair</span>
                        </a>                        
                    </li>
                </ul>
            </nav>
        </div>

        <script>
            // Maintain Scroll Position
            if (typeof localStorage !== 'undefined') {
                if (localStorage.getItem('sidebar-left-position') !== null) {
                    var initialPosition = localStorage.getItem('sidebar-left-position'),
                        sidebarLeft = document.querySelector('#sidebar-left .nano-content');
                    
                    sidebarLeft.scrollTop = initialPosition;
                }
            }
        </script>
        

    </div>

</aside>