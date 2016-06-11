<?php

namespace SroScraper\Command;

use SroScraper\Models\SRO;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCommand extends Command
{
    protected function configure()
    {
      $this
        ->setName('sro:update')
        ->setDescription('Update SRO data from the legacy source (scraping)')
      ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
      $client = new Client();
      $guzzleClient = new \GuzzleHttp\Client(array(
          'curl' => array(
              CURLOPT_SSL_VERIFYHOST => 0,
              CURLOPT_SSL_VERIFYPEER => 0,
          ),
      ));
      $client->setClient($guzzleClient);

      $this->_extractSRO($client);
    }

    private function _extractSRO($client)
    {
      $queryParams = array(
        '_search' => 'false',
        'nd' => '1463054239206',
        'rows' => 20,
        'page' => 1,
        'sidx' => null,
        'sord' => 'asc'
      );

      $queryCurrentPage = 1;
      $queryTotalPage = 0;

      do
      {
        $queryParams['page'] = $queryCurrentPage;

        $response = $client->getClient()->request(
          'POST',
          'https://sro.gosnadzor.ru/Home/SroData',
          [
            'form_params' => $queryParams
          ]
        );

        $data = json_decode($response->getBody());

        foreach($data->rows as $row)
        {
          $sroId = $this->_extractSROId($row->name);
          $this->_extractSROMember(
            $client,
            'https://sro.gosnadzor.ru/Home/SroDetails?id=' . $sroId
          );
        }

        // Update page indexes
        if (!$queryTotalPage)
        {
          $queryTotalPage = $data->total;
        }
        $queryCurrentPage++;
      } while($queryCurrentPage < $queryTotalPage);

    }

    private function _extractSROMember($client, $url)
    {
      $crawler = $client->request('GET', $url);
      $currentItn = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[5]/td[2]')->text());

      $sro = SRO::where('itn', $currentItn)->first();

      if (!$sro) {
        $sro = new SRO;
      }

      $sro->title = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[1]/td[2]')->text());
      $sro->city = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[2]/td[2]')->text());
      $sro->activity = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[3]/td[2]')->text());
      $sro->short_title = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[4]/td[2]')->text());
      $sro->itn = $currentItn;
      $sro->psrn = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[6]/td[2]')->text());
      $sro->sro_members_count = intval(trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[7]/td[2]')->text()));
      $sro->sro_members_excluded_count = intval(trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[8]/td[2]')->text()));
      $sro->compensation_fund = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[9]/td[2]')->text());
      $sro->legal_address = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[10]/td[2]')->text());
      $sro->street_address = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[11]/td[2]')->text());
      $sro->phone = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[12]/td[2]')->text());
      $sro->fax = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[13]/td[2]')->text());
      $sro->email = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[14]/td[2]')->text());
      $sro->site = trim($crawler->filterXPath('//*[@id="tabs-1"]//table//tr[15]/td[2]')->text());
      $sro->state_registry_no = trim($crawler->filterXPath('//*[@id="tabs-2"]//table//tr[4]/td[2]')->text());
      $sro->state_registry_decision_no = trim($crawler->filterXPath('//*[@id="tabs-2"]//table//tr[5]/td[2]')->text());
      $sro->state_registry_inclusion_date = trim($crawler->filterXPath('//*[@id="tabs-2"]//table//tr[6]/td[2]')->text());
      $sro->head_html = trim($crawler->filter('#tabs-4')->html());
      $sro->sro_activity_html = trim($crawler->filter('#tabs-5')->html());
      $sro->sro_rules_html = trim($crawler->filter('#tabs-6')->html());
      $sro->save();
    }

    private function _extractSROId($s)
    {
      $re = "/(id=(?P<id>\\d+))/";
      preg_match($re, $s, $matches);

      return $matches['id'];
    }
}
