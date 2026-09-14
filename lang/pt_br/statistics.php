<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * statistics.php
 *
 * @package   mod_statistics
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['calculationtype'] = 'Variância e desvio padrão';
$string['clear'] = 'Limpar';
$string['count'] = 'Quantidade';
$string['inputhelp'] = 'Use espaços, ponto e vírgula, vírgulas entre itens ou uma linha por valor. Para decimais, você pode usar 10,5 ou 10.5.';
$string['invalidvalue'] = 'Há um valor que não pôde ser interpretado como número.';
$string['labeldata'] = 'Cole ou digite os números';
$string['maximum'] = 'Máximo';
$string['mean'] = 'Média';
$string['median'] = 'Mediana';
$string['minimum'] = 'Mínimo';
$string['mode'] = 'Moda';
$string['modulename'] = 'Estatística rápida';
$string['modulenameplural'] = 'Estatísticas rápidas';
$string['needtwovalues'] = 'A variância amostral precisa de pelo menos dois valores.';
$string['noactivities'] = 'Ainda não há atividades de Estatística rápida neste curso.';
$string['nomode'] = 'Sem moda';
$string['placeholder'] = 'Ex.: 12 18 18 21 25 30
ou um valor por linha';
$string['pluginadministration'] = 'Administração da Estatística rápida';
$string['pluginname'] = 'Estatística rápida';
$string['population'] = 'População (divide por n)';
$string['privacy:metadata'] = 'O plugin Estatística rápida não armazena os números informados pelo usuário nem outros dados pessoais.';
$string['range'] = 'Amplitude';
$string['sample'] = 'Amostra (divide por n − 1)';
$string['sortedvalues'] = 'Valores em ordem';
$string['standarddeviation'] = 'Desvio padrão';
$string['statistics:addinstance'] = 'Adicionar uma nova Estatística rápida';
$string['statistics:view'] = 'Usar a Estatística rápida';
$string['statisticsname'] = 'Nome da atividade';
$string['sum'] = 'Soma';
$string['variance'] = 'Variância';
$string['varianceexplanation'] = 'A variância é calculada a partir da soma dos quadrados das diferenças de cada valor em relação à média.';
