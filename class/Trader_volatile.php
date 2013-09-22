<?php 

class 					Trader {

	private 			$actualValue;
	private 			$mobileAverage;

	private 			$totalDay;
	private 			$totalMoney;

	private 			$nbActions = 0;
	private 			$money;

	private 			$historyCours = array();
	private 			$historyGain = array();

	public function		__construct() {

	}

	public function 	setTotalDay($totalDay) {
		$this->totalDay = $totalDay;
	}

	public function 	getTotalDay() {
		return ($this->totalDay);
	}

	public function 	getTotalMoney() {
		return ($this->totalMoney);
	}

	public function 	setTotalMoney($totalMoney) {
		$this->totalMoney = $totalMoney;
		$this->money = $totalMoney;
	}

	public function 	calculPriceBuy($nbActions) {
		$price = $nbActions * $this->actualValue;
		$tax = ceil($price * 0.0015);
		return ($price + $tax);
	}

	public function 	calculPriceSell($nbActions) {
		$price = $nbActions * $this->actualValue;
		$tax = ceil($price * 0.0015);
		return ($tax);
	}

	public function 	canBuy($nbActions) {
		if ($this->calculPriceBuy($nbActions) <= $this->money) {
			return (true);
		} else {
			return (false);
		}
	}

	public function 	qtyMaxBuy() {
		$i = 0;
		while ($this->canBuy($i))
			$i++;
		return ($i - 1);
	}

	public function 	canSell($nbActions) {
		if ($this->nbActions >= $nbActions && $this->calculPriceSell($nbActions) <= $this->money) {
			return (true);
		} else {
			return (false);
		}
	}

	public function 	buy($nbActions) {
		echo "buy " . $nbActions . "\n";
		$this->money -= $this->calculPriceBuy($nbActions);
		$this->nbActions += $nbActions;
	}

	public function 	sell($nbActions) {
		echo "sell " . $nbActions . "\n";
		$this->money -= $this->calculPriceSell($nbActions);
		$this->money += $this->actualValue * $nbActions;
		$this->nbActions -= $nbActions;
	}

	public function 	mobileAverage($days) {
		if (count($this->historyCours) >= $days) {
			$sum = 0;
			$i   = count($this->historyCours) - 1;
			$j   = 0;
			while ($j < $days) {
				$sum += $this->historyCours[$i];
				$i--;
				$j++;
			}
			return ($sum / $days);
		} else {
			return ("0");
		}
	}

	public function 	mobileAverageDiff($days) {
		return ($this->mobileAverage($days) - $this->actualValue);
	}

	public function 	mobileAverageDiffPercent($days) {
		return (100 - (($this->actualValue / $this->mobileAverage($days)) * 100));
	}

	public function 	trade($cours, $day) {
		$this->actualValue        = $cours;
		$this->historyCours[$day] = $cours;
		

		Debugger::debug("Nb jours tot : " . $this->totalDay . "\t\t" . $day);
		Debugger::debug("Nb Actions : " . $this->nbActions . "\tNb. Achats : " . $this->qtyMaxBuy() . "\tCours : " . $cours . "\tMobile Average : " . number_format($this->mobileAverage(20), 6) . "\t\tDiff :: " . $this->mobileAverageDiff() . "\t\tDiffPercent :: " . $this->mobileAverageDiffPercent());
		if ($day > 9 && $day != $this->totalDay - 1) {

			if ($this->mobileAverageDiffPercent(10) > 0 && $this->mobileAverageDiffPercent(5) > 0 && $this->nbActions > 0) {
				if ($this->canSell(1))
					$this->sell(1);
				else
					echo "wait\n";
			} else if ($this->mobileAverageDiffPercent(10) < 0 && $this->mobileAverageDiffPercent(5) < 0 && $this->canBuy(1)) {
				$this->buy(1);
			} else {
				echo "wait\n";
			}

		} else if ($day == $this->totalDay - 1 && $this->nbActions > 0) {
			Debugger::debug("Nb Actions : " . $this->nbActions . "\tCours : " . $cours . "\tMobile Average : " . number_format($this->mobileAverage(20), 6) . "\t\tDiff :: " . $this->mobileAverageDiff() . "\t\tDiffPercent :: " . $this->mobileAverageDiffPercent());
			if ($this->canSell($this->nbActions)) {
				$this->sell($this->nbActions);
			} else {
				$this->sell($this->nbActions -1);
			}
		} else  {
			echo "wait\n";
		}
	}


}

?>