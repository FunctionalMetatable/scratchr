<!-- Displays Welcome to Scratch Community message for new accounts. Rendered by /app/views/users/myscratchr.thtml -->

<div class="mystuff_container">
	<div class="mystuff_header">
		<h3>Welcome to the Scratch Community</h3>
	</div>

<!-- Should match user_projects from here on out -->
       	<div class="mystuff_content">
		<table border="0" cellpadding="10" align=left style="padding:0px 20px;">
			<tbody>
				<tr>
					<td align=left style="padding:0;">
						<br><b>Questions about Scratch?</b><br><br>
Check out the <a href="<?php echo $html->url('/redirect/scratch_faq')?>">Scratch FAQ</a>, or ask the community in the <a href="<?php echo $html->url('/redirect/about_scratch')?>">All About Scratch</a> forum.
					</td>
				</tr>
				
			</tbody>
		</table>

	</div>
</div>
