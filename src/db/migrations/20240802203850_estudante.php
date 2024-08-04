<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class Estudante extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('estudante');
            $table  ->addColumn('id_estudante', 'integer', ['limit' => 11])
                    ->addColumn('usuario', 'string', ['limit' => 255])
                    ->addColumn('email', 'string', ['limit' => 255])
                    ->addColumn('senha', 'string', ['limit' => 255])
                    ->addIndex(['id_estudante'], ['unique' => true])
                    ->create();
    }
}
