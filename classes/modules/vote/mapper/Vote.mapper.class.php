<?php

/**
 * ShowVotes
 * by Stanislav Nevolin, stanislav@nevolin.info
 */
class PluginShowvotes_ModuleVote_MapperVote extends Mapper
{
    public function GetTopicVotersByFilter($sTargetId, $aFilter, &$iCount, $iLimit)
    {
        $table = Config::Get('db.table.vote');

        $sWhere = $this->buildFilter($aFilter);

        $sql = "
			SELECT * FROM {$table}
			WHERE
					target_type = 'topic'
				AND
					target_id = ?d
				{$sWhere}
		";

        if (is_int($iLimit) && $iLimit > -1) {
            $sql .= " LIMIT {$iLimit}";
        }

        $aResult = array();

        $aVotes = $this->oDb->selectPage($iCount, $sql, $sTargetId);
        if ($aVotes) {
            foreach ($aVotes as $aVote) {
                $aResult[] = Engine::GetEntity('ModuleVote_EntityVote', $aVote);
            }
        }
        return $aResult;
    }

    protected function buildFilter($aFilter)
    {
        $sWhere = "";
        if (isset($aFilter['direction'])) {
            switch ($aFilter['direction']) {
                case 'positive':
                    $sWhere .= " AND vote_direction > 0";
                    break;
                case 'negative':
                    $sWhere .= " AND vote_direction < 0";
                    break;
                case 'neutral':
                    $sWhere .= " AND vote_direction = 0";
                    break;
            }
            return $sWhere;
        }
    }
}
