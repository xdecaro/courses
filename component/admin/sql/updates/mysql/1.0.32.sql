-- Courses 1.0.32
-- Add a dedicated featured flag for editions without changing publication/state data.
ALTER TABLE `#__decarocourses_editions`
  ADD COLUMN `featured` tinyint NOT NULL DEFAULT 0 AFTER `state`,
  ADD KEY `idx_featured` (`featured`);
