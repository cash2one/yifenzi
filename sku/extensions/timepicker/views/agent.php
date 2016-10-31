<?php if(is_object($this->model)): ?>
<input type="text" class="input_box" id="<?php echo $this->id; ?>" value="<?php echo $this->model->{$this->name}?$this->model->{$this->name}:$this->options['value']; ?>" name="<?php echo get_class($this->model).(!empty($this->options['tabularLevel'])?$this->options['tabularLevel']:'').'['.$this->name.']'; ?>" />
<?php else: ?>
  <input type="text" class="input_box" id="<?php echo $this->id; ?>" value="<?php echo $this->options['value']; ?>" name="<?php echo $this->name; ?>" />
<?php endif; ?>