-- Workflow by Review Type
-- Traces workflow relationships across related tables while limiting results to current revisions.
--
-- Portfolio note: This public sample is based on code I originally wrote for an
-- internal business tool. Proprietary names, endpoints, identifiers, sample data,
-- and environment-specific values have been replaced with generic equivalents.
-- Search term for the linked workflow I am looking for
DECLARE @searchTerm varchar(255)
Set @searchTerm = '%SampleLinkedWorkflow%'



select distinct


-- Pull the latest revision along with the parent and linked workflow information
wr.Rev as Max_Rev
,prg.Name as program
,w.Name as ParentWorkflowName
,w2.name as LinkedWorkflowName
,w.WorkflowId
,wn.WorkflowNodeId
,br.BranchId
--,t.TokenKey
--,d.DataItemKey
--,br.ChildWorkflowNodeId

--,d1.DefaultValue

-- These fields can be uncommented when I need to dig further into token/data-item configuration
--,din.Name as [Index]
--,dsc.Name as scope
--,dl.Name as [Source]
--,d.DataItemKey as Source_Key
--,d.DefaultValue
--,ob.[KEY] as [DeterminationValue]
--,expressiondata.DefaultValue
--,ex.ExpressionId
--,ex.ArgumentOneId

-- Keep the user tied to the latest revision for troubleshooting/auditing
,rev.ModifiedByUserID

from WorkflowRevision rev (NOLOCK)

-- Get the highest revision number for each workflow so I am only looking at the newest version
INNER JOIN
(
SELECT WorkflowId, MAX(RevisionNumber) AS Rev
FROM WorkflowRevision
GROUP BY WorkflowId
) WR
ON rev.WorkflowId = wr.WorkflowId AND rev.RevisionNumber = wr.Rev

-- Join the latest revision back to the workflow and its nodes
join Workflow w (NOLOCK) on w.WorkflowId = wr.WorkflowId
left join [WorkflowRevisionNode] wn (NOLOCK) on wn.WorkflowRevisionId = rev.WorkflowRevisionId -- Needed to get the workflow node ID

-- Follow the branch/node relationship so I can see where the workflow links out
left join Branch br on br.ChildWorkflowNodeId = wn.WorkflowNodeId
--join BranchCondition bc on bc.BranchId = br.BranchId -- Uncomment when I need the condition/qualifier information for the branch

-- Use the node link to identify the workflow connected to this branch
left join WorkflowNodeLink NL on NL.WorkflowNodeId = br.ChildWorkflowNodeId
join Workflow w2 on w2.WorkflowId = nl.LinkedWorkflowId

-- Optional token/data-item joins are left here for deeper troubleshooting when needed
left join Token t (NOLOCK) on t.WorkflowNodeId = wn.WorkflowNodeId
--left join DataItem d (NOLOCK) on d.DataItemId = bc.DataItemId -- Uncomment when I need the data item tied to the branch condition
--left join DataItem d1 on d1.DataItemId = bc.ValueDataItemId -- Uncomment when I need the value used by the branch condition

-- Pull observation/expression information tied to the workflow node
left join WorkflowNodeObservation wno (NOLOCK) on wno.WorkflowNodeId = wn.WorkflowNodeId
left join Observation ob (NOLOCK) on ob.ObservationId = wno.ObservationId
left join [Expression] ex (NOLOCK) on ex.ExpressionId = ob.ExpressionId
left join ExpressionArgument ea (NOLOCK) on ea.ExpressionArgumentId = ex.ArgumentOneId -- Can also check ArgumentTwoId when needed
left join DataItem expressiondata on expressiondata.DataItemId = ea.ValueDataItemId

-- Tie the workflow back to the program it belongs to
left join ProgramWorkflow pg (NOLOCK) on pg.WorkflowId = rev.WorkflowId
left join Program prg (NOLOCK) on prg.ProgramId = pg.ProgramId

-- These joins can be used if I need more detail about where a data item comes from
--left join DataItemIndex din (NOLOCK) on din.DataItemIndexId = d.DataItemIndexId
--left join DataItemScope dsc (NOLOCK) on dsc.DataItemScopeId = d.DataItemScopeId
--left join DataItemLocation dl (NOLOCK) on dl.DataItemLocationId = d.DataItemLocationId


where
--w.Name like @searchTerm -- Use this when I want to see everything linked from a specific parent workflow
w2.name like @searchTerm -- Use this when I want to find every parent workflow that links to the workflow I searched for
and prg.[Name] not in ('Archived Workflows') -- Leave archived/retired workflows out of the results
order by WorkflowNodeId desc
