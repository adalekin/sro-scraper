<?php

namespace SroScraper\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;


class SchemaCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('schema:create')
            ->setDescription('Building schema')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Droping Schema
        Capsule::schema()->dropIfExists('sro');

        // Creating schema
        Capsule::schema()->create('sro', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('city');
            $table->text('activity');
            $table->string('short_title');
            $table->string('itn');
            $table->string('psrn');
            $table->integer('sro_members_count');
            $table->integer('sro_members_excluded_count');
            $table->integer('compensation_fund');
            $table->text('legal_address');
            $table->text('street_address');
            $table->string('phone');
            $table->string('fax');
            $table->string('email');
            $table->string('site');
            $table->string('state_registry_no');
            $table->string('state_registry_decision_no');
            $table->string('state_registry_inclusion_date');
            $table->text('head_html');
            $table->text('sro_activity_html');
            $table->text('sro_rules_html');
            $table->timestamps();
        });
    }
}
