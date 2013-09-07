<?php

//$zaciatok = new skumavkyStav(array('A'=>10, 'B'=>0, 'C'=>0, 'D'=>0, 'E'=>10));
$zaciatok = new skumavkyStav(array('A'=>16, 'B'=>0, 'C'=>0));

//$koniec = new skumavkyStav(array('A'=>5, 'B'=>5, 'C'=>5, 'D'=>3, 'E'=>2));
$koniec = new skumavkyStav(array('A'=>8, 'B'=>8, 'C'=>0));

class skumavkyStav {
  //private $skumavky = array('A'=>10, 'B'=>6, 'C'=>9, 'D'=>3, 'E'=>10);
  private $skumavky = array('A'=>16, 'B'=>9, 'C'=>7);

  protected $stav = null;
  protected $from = null;
  public $fromOperation =null;

  public function __construct($stav) {
    if (!is_array($stav) || count($stav)!=count($this->skumavky)) {
      echo 'ERROR - ziadne skumavky, alebo zly pocet'."\n";
      return;
    }
 
    foreach ($stav as $skumavka=>$hladina) {
      if (!isset($this->skumavky[$skumavka])) {
        echo 'ERROR - skumavka '.$skumavka.' neexistuje'."\n";
        return;
      }
      if ($this->skumavky[$skumavka]<$hladina) {
        echo 'ERROR - hladina ('.$hladina.') v skumavke ('.$skumavka.') je vacsia ako maximalna ('.$this->skumavky[$skumavka].')'."\n";
        return;
      }
      $this->stav = $stav;
    }
  }

  public function setFrom(skumavkyStav $skumavkyStav, $operation = null) {
    $this->from = $skumavkyStav;
    $this->fromOperation = $operation;
  } 

  public function getFrom() {
    return $this->from;
  }

  public function __toString() {
    $ret = '';
    foreach ($this->stav as $skumavka=>$hladina) {
      $ret .= $skumavka.'='.$hladina.'; ';
    }
    return $ret;
  }

  public function getAllNextStates() {
    $ret = array();
    foreach ($this->stav as $fromSkumavka=>$fromHladina) {
      foreach ($this->stav as $toSkumavka=>$toHladina) {
        if ($fromSkumavka==$toSkumavka) continue;
        if ($fromHladina==0) continue;
        $toMaxHladina=$this->skumavky[$toSkumavka];
        $toRozdiel = $toMaxHladina-$toHladina;
        if ($toRozdiel==0) continue;
        $kolkoPrelievam = min($fromHladina, $toRozdiel);
        $newFromHladina = $fromHladina - $kolkoPrelievam;
        $newToHladina = $toHladina + $kolkoPrelievam;
        $novyStavArray=array();
        foreach ($this->stav as $s=>$h) {
          $novyStavArray[$s]=$h;
        }
        $novyStavArray[$fromSkumavka]=$newFromHladina;
        $novyStavArray[$toSkumavka]=$newToHladina;
        $novyStav = new skumavkyStav($novyStavArray);
        $novyStav->setFrom($this, $fromSkumavka.'->'.$toSkumavka);
        $ret[]=$novyStav;
      }
    }
    return $ret;
  }
}
$stavovePole = array ((string)$zaciatok=>$zaciatok);

for ($i=0; $i<count($stavovePole); $i++) {
  $keys = array_keys($stavovePole);
  $stav=$stavovePole[$keys[$i]];
  $dalsieStavy = $stav->getAllNextStates();
  foreach ($dalsieStavy as $dalsiStav) {
    if ((string)$dalsiStav == (string)$koniec) {
      $koniec = $dalsiStav;
      break 2;
    }
    if (!isset($stavovePole[(string)$dalsiStav])) {
      $stavovePole[(string)$dalsiStav]=$dalsiStav;
    }
  }
}

if ($koniec->getFrom()===null) {
  echo 'Uloha nema riesenie'."\n";
} 
else {
  echo 'Naslo sa riesenie'."\n"; 
  $cesta = array();
  $som = $koniec;
  while ((string)$som != (string)$zaciatok) {
    $cesta[]=$som;
    $som=$som->getFrom();
  }
  $cesta = array_reverse($cesta);
  echo (string)$zaciatok."\n";
  foreach ($cesta as $stav)
    echo $stav->fromOperation."\n".$stav."\n";
}

?>
