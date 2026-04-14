-- =====================================================
-- INSERT 20 usuarios jugadores de futbol
-- Password de todos: 123456
-- status 10 = ACTIVE
-- =====================================================

INSERT INTO `user` (`username`, `auth_key`, `password_hash`, `email`, `status`, `created_at`, `updated_at`) VALUES
('garcia_lucas',    'AdUBVtzkT1zsyoma0KSeVGMVOpEnztlY', '$2y$13$Slnztd96Z/2mwB5nb/jpPemaSbY69qISyh4G7q1dJZuev64WMmzgO', 'garcia.lucas@liga.com',    10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('martinez_pablo',  'Ju4UgoRPIzv5mC4A9PKzlV87SoCI3JGP', '$2y$13$0T5iFJFxebs9fwAsTYkWH.HbLHZoafrc1O2VmFSf7WB8VloFEcGZ2', 'martinez.pablo@liga.com',  10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('rodriguez_diego', 'lyabiNxHBu6EDS4hYbo8jMRlYsLUbR2i', '$2y$13$pqBO/IhlBSTP4m1tyehCsOMtwUdX5mzNFkqLZCIy5C5KfrKJGIP3a', 'rodriguez.diego@liga.com', 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('lopez_franco',    'B1TxOuKpk12M5OzwdGzAJXTXL8pXecF5', '$2y$13$sizCJEd/VjcSBV5mwVT9K.57GV.h6ykcCIILHFhNiuVj/em.Eh6sW', 'lopez.franco@liga.com',    10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('gonzalez_maxi',   'vod8JQjwPPLNDGstn6TtSEPnz6jz6qRn', '$2y$13$lrWNibC0alP09NTdop4p8.zQC6Y6fARrUNFpQivuXM4V75tJHXhpq', 'gonzalez.maxi@liga.com',   10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('fernandez_tomas', 'otkHYIpqn09zLOoctWOK5VRsnMykKsYo', '$2y$13$oDv5Beo6TLRPQFxF.Jzc1Osotf0qQaa21.wLqbCo70XKpnuwTconm', 'fernandez.tomas@liga.com', 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('perez_nicolas',   '8kCQgoSHtEvO4Swu1hQCzmGjimRl9pSa', '$2y$13$2.fiwuke2XDsTZyRQHvke.56Sh/Cfusr9dQL2Ctse5/Swssh/JH2q',  'perez.nicolas@liga.com',   10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('diaz_sebastian',  'IweaQURPwt6DTa253kGjjEld9EzqrtpA', '$2y$13$lL/zTU6PZ5eQ7mWipYjujenIOxF6vnSLfPl/m/7nMTogibTwRbble',  'diaz.sebastian@liga.com',  10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('torres_ezequiel', 'oS4rWr4zXyozZ5c4N7R9sujm7PmO2Wrw', '$2y$13$fG1nPQEGMHUicBqCZ4YZJufOSLenNHPGGmPO2VAKcbE6SXQIbSibO', 'torres.ezequiel@liga.com', 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('romero_brian',    '3gDjr7RXV5x6TjOcfJcHf69YWgaExWSV', '$2y$13$GmkTKs5zbwZmakNDZdecnutLUpHUvWXis6I92qfO2HGqnNvBZ9Afe',  'romero.brian@liga.com',    10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('sanchez_leandro', 'wPHvA55WoS5xtj1YUrHgJktNo4lUnfOt', '$2y$13$H1l.JoJlKiO8Q6WQpGoabOAja.G/h.PM7ZhthcpPqS35FJaGeXAXK', 'sanchez.leandro@liga.com', 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('vargas_matias',   '4IytJv2v1x8XMPFhzU8I6LYasJdOWpMn', '$2y$13$DjnyjiluMeW6YKi7pXqh8.fKrz6IUaX4f0BX3Si5rEI.PYDPqqLOu', 'vargas.matias@liga.com',   10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('castillo_emilio', 'Unc2sbfKATqC2EuYtVH5RJeSBvvYKCDQ', '$2y$13$Fhuxxy05dLE0tEn6yW8DauZLYBs/uiO2/qTKJ66VmwRsAIHNUq.Ca', 'castillo.emilio@liga.com', 10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('moreno_rodrigo',  'Jw16cHTkHjj60LWfHx0wCcHIzr23cfd2', '$2y$13$eUtqdHd4QVT4BD2bqdyqOO1nKl1dYzfd3y2JkmbzFES3B.4d4sQny', 'moreno.rodrigo@liga.com',  10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('herrera_ivan',    'FeZshDmXG6oYSjCsTYOPsz718QKzXl0q', '$2y$13$vqKulN04IBdOuwNR8/g3Wu/Kbw7Y6c1Q8dJNg.9foNx4mb0tV2veS', 'herrera.ivan@liga.com',    10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('silva_cristian',  '9ZeOKc4VBcqX7bvBuSxW2cqXRWQPbqkM', '$2y$13$VDLIvFkb8VACL8sIhcu20OSij6ylfX2WBsdN/W4fS0tGGfdEHJG96', 'silva.cristian@liga.com',  10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('medina_jonatan',  'rCBjaZf3vYHitz7BcNpzqZpbtHwDmshM', '$2y$13$EEYTznxVfQQ6utpU683Hn.CNCzD1qSLJcdm42ikZU4V.7ND8exTKq', 'medina.jonatan@liga.com',  10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('acosta_walter',   '3G9jIbG6WrNwj1v3ntIRPYh9OBzqpmrD', '$2y$13$N1mBlw2LT8nv9mL5KoReLOyuIXYICWj0OMvo3t375aB8gJtj2NGlG', 'acosta.walter@liga.com',   10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('benitez_fabian',  'czkZsjcT6SzxfLYI6KiueNtANnhlMFdG', '$2y$13$l4iAJFtEztcBWiezmNn9iea2L11VNygOa5gAe6NyOrQGdSmqf.otW', 'benitez.fabian@liga.com',  10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
('reyes_gustavo',   'GSQXjHEx38XFC6JHH6cBGaBFIx6ngP2h', '$2y$13$m39muVkam0CbV70TaszleOkOl0cLgFMumlZphQLgaMmgu0H/mtFta',  'reyes.gustavo@liga.com',   10, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- =====================================================
-- Asignar rol 'jugador' a todos los usuarios insertados
-- Ajusta los IDs segun los que genero el INSERT anterior
-- =====================================================

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`)
SELECT 'jugador', id, UNIX_TIMESTAMP()
FROM `user`
WHERE username IN (
    'garcia_lucas', 'martinez_pablo', 'rodriguez_diego', 'lopez_franco',
    'gonzalez_maxi', 'fernandez_tomas', 'perez_nicolas', 'diaz_sebastian',
    'torres_ezequiel', 'romero_brian', 'sanchez_leandro', 'vargas_matias',
    'castillo_emilio', 'moreno_rodrigo', 'herrera_ivan', 'silva_cristian',
    'medina_jonatan', 'acosta_walter', 'benitez_fabian', 'reyes_gustavo'
);
