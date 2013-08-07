<?php
function TokenizeJSON($JSON)
{
$Tokens=array();
$TokenIndex=0;
$CurToken="";
$Quoted=false;
$Escaped=false;
for($i=0;$i<strlen($JSON);$i++)
{
	switch($JSON[$i])
	{
	case "\\":
	if($Escaped)
	{
	$CurToken=$CurToken."\\";
	$Escaped=false;
	}
	else $Escaped=true;
	break;
	case "\"":
	if(!$Escaped)
	{
	if($Quoted)$Quoted=false;
	else $Quoted=true;
	}
	else
	{
	$CurToken=$CurToken."\"";
	$Escaped=false;
	}
	break;
	case "{":
	case "}":
	case "[":
	case "]":
	case ",":
	case ":":
	if(!$Escaped&&!$Quoted)
	{
	if(strlen($CurToken)>0)
		{
		$Tokens[$TokenIndex]=$CurToken;
		$CurToken="";
		$TokenIndex++;
		}
		$Tokens[$TokenIndex]=$JSON[$i];
		$TokenIndex++;
		$CurToken="";
	break;
	}
	default:
	$CurToken=$CurToken.$JSON[$i];
	$Escaped=false;
	break;
	}
}
return $Tokens;
}

function ParseLiteral($Literal)
{
return $Literal;
}
function ParseJSONArray($Tokens,&$TokenIndex)//TODO- fix this
{
$Array=array();
$ArrayIndex=0;
$TokenIndex++;
	while($TokenIndex<count($Tokens))
	{
	if($Tokens[$TokenIndex]=="]")break;
		switch($Tokens[$TokenIndex])
		{
		case "}":
		case ":":
		echo "Unexpected colon\n";
		return false;
		break;
		case "{":
		$Value=ParseJSONObject($Tokens,$TokenIndex);
		if($Value===false)
		{
		echo "Failed parsing object\n";
		return false;
		}		
		$Array[$ArrayIndex]=$Value;
		$ArrayIndex++;
		break;
		case "[":
		$Value=ParseJSONArray($Tokens,$TokenIndex);
		if($Value===false)
		{
		echo "Failed parsing array\n";		
		return false;
		}		
		$Array[$ArrayIndex]=$Value;
		$ArrayIndex++;
		break;
		case ",":
		break;
		default:
		$Array[$ArrayIndex]=ParseLiteral($Tokens[$TokenIndex]);
		$ArrayIndex++;
		break;
		}
	$TokenIndex++;
	}
return $Array;
}

function ParseJSONObject($Tokens,&$TokenIndex)//TODO- fix this
{
$Object=array();
$Key="";
$CurIsKey=true;
$TokenIndex++;
	while($TokenIndex<count($Tokens))
	{
	if($Tokens[$TokenIndex]=="}")break;
		switch($Tokens[$TokenIndex])
		{
		case "{":
		if($Key!=""&&!$CurIsKey)
		{
		$Value=ParseJSONObject($Tokens,$TokenIndex);
		if($Value===false)
		{
		echo "Failed parsing object\n";
		return false;		
		}
		$Object[$Key]=$Value;
		}
		else
		{
		echo "Attempted to use object as key or no key provided\n";
		return false;
		}
		break;
		case "[":
		if($Key!=""&&!$CurIsKey)
		{
		$Value=ParseJSONArray($Tokens,$TokenIndex);
		if($Value===false)
		{
		echo "Failed parsing array\n";
		return false;
		}		
		$Object[$Key]=$Value;
		}
		break;
		case "]":
		echo "Unexpected array termination\n";
		return false;
		break;
		case ",":
		if($CurIsKey)
		{
		echo "Unexpected comma\n";
		return false;
		}
		$CurIsKey=true;
		break;
		case ":":
		if(!$CurIsKey)
		{
		echo "Unexpected colon\n";
		return false;
		}
		$CurIsKey=false;
		break;
		default:
		if($CurIsKey)$Key=$Tokens[$TokenIndex];
		else $Object[$Key]=ParseLiteral($Tokens[$TokenIndex]);
		break;
		}
	$TokenIndex++;
	}
return $Object;
}

function ParseJSON($JSON)
{
$TokenIndex=0;
$Tokens=TokenizeJSON($JSON);
if($Tokens[0]=="{")return ParseJSONObject($Tokens,$TokenIndex);
else if($Tokens[0]=="[")return ParseJSONArray($Tokens,$TokenIndex);
else return false;
}
?>
