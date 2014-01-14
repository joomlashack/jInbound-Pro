<?php
/**
 * @package		JInbound
 * @subpackage	com_jinbound
@ant_copyright_header@
 */

defined('JPATH_PLATFORM') or die;

?>
<div class="row-fluid">
	<div class="span12">
		<div class="row-fluid">
			<div class="span12">
			
				<div class="row-fluid">
					<div class="span12 well">
						<div class="row-fluid">
							<div class="pull-right"><?php echo JText::sprintf('COM_JINBOUND_USER_ID', $this->item->contact_id); ?></div>
						</div>
						<div class="row-fluid">
							<?php
								$this->_currentFieldset = $this->form->getFieldset('leadname');
								foreach ($this->_currentFieldset as $field) :
							?>
							<div class="span6">
								<?php
									$this->_currentField = $field;
									echo $this->loadTemplate('edit_field');
								?>
							</div>
							<?php
								endforeach;
							?>
						</div>
					</div>
				</div>
				
				<div class="row-fluid">
					<div class="span12">
						<?php
							$this->_currentFieldset = $this->form->getFieldset('profile');
							echo $this->loadTemplate('edit_fields');
						?>
					</div>
				</div>
				
				<div class="row-fluid">
					<div class="span12 well">
					
						<div class="row-fluid">
							<h3><?php echo JText::_('COM_JINBOUND_LEAD_DETAILS'); ?></h3>
						</div>
						
						<div class="row-fluid">
							<div class="span12">
								<?php
									$this->_currentFieldset = $this->form->getFieldset('details');
									echo $this->loadTemplate('edit_fields');
								?>
							</div>
						</div>
						
						<div class="row-fluid">
							<div class="span6">
								<h4><?php echo JText::_('COM_JINBOUND_FORM_INFORMATION'); ?></h4>
								<div class="well">
									<?php echo JHtml::_('jinbound.startSlider', 'leadSlider'); ?>
									<?php if (!empty($this->item->_formdatas)) : ?>
									<?php foreach ($this->item->_formdatas as $i => $data) : ?>
									<?php echo JHtml::_('jinbound.addSlide', 'leadSlider', $data['created'] . ' | ' . $data['pagename'], 'leadslider-' . $i); ?>
									<table class="table table-striped">
										<?php if (array_key_exists('lead', $data)) foreach ($data['lead'] as $key => $value) : ?>
										<tr>
											<td><?php echo $this->escape($key); ?></td>
											<td><?php echo $this->escape(print_r($value, 1)); ?></td>
										</tr>
										<?php endforeach; ?>
										<?php if (!empty($data['ip'])) : ?>
										<tr>
											<td><?php echo JText::_('COM_JINBOUND_IP'); ?></td>
											<td><?php echo $this->escape($data['ip']); ?></td>
										</tr>
										<?php endif; ?>
									</table>
									<?php echo JHtml::_('jinbound.endSlide'); ?>
									<?php endforeach; ?>
									<?php endif; ?>
									<?php echo JHtml::_('jinbound.endSlider'); ?>
								</div>
								<h4><?php echo JText::_('COM_JINBOUND_CURRENT_LEAD_NURTURING_CAMPAIGNS'); ?></h4>
								<div class="well">
									<?php if (is_object($this->campaign)) : ?>
									<?php if (!empty($this->campaign->campaign)) : ?>
									<h5><?php echo $this->escape($this->campaign->campaign->name); ?></h5>
									<?php if (!empty($this->campaign->emails)) : ?>
									<table class="table table-striped">
										<?php foreach ($this->campaign->emails as $email) : ?>
										<tr>
											<td><a href="<?php echo JInboundHelperUrl::edit('email', $email->id); ?>"><?php echo $this->escape($email->name); ?></a></td>
											<td><?php echo ($email->sent ? $this->escape($email->sent->format(DateTime::RSS)) : ''); ?></td>
										</tr>
										<?php endforeach; ?>
									</table>
									<?php endif; ?>
									<?php endif; ?>
									<?php endif; ?>
								</div>
							</div>
							<div class="span6">
								<h4><?php echo JText::_('COM_JINBOUND_NOTES'); ?></h4>
								<div class="pull-right">
									<?php echo JHtml::_('jinbound.leadnotes', $this->item->id, true); ?>
								</div>
								<div class="well">
									<table id="jinbound_leadnotes_table" class="table table-striped">
										<tbody>
										<?php if (!empty($this->notes)) : foreach ($this->notes as $note) : ?>
											<tr>
												<td><span class="label"><?php echo $note->created; ?></span></td>
												<td class="note"><?php echo $this->escape($note->text); ?></td>
											</tr>
										<?php endforeach; endif; ?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
